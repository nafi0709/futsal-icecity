<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index()
    {
        $fields = Field::where('is_active', true)->get();
        
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['field', 'payment'])
            ->latest()
            ->get();
        
        return view('user.dashboard', compact('fields', 'bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ], [
            'field_id.required' => 'Pilih lapangan terlebih dahulu',
            'field_id.exists' => 'Lapangan tidak valid',
            'start_time.required' => 'Waktu mulai harus diisi',
            'end_time.required' => 'Waktu selesai harus diisi',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
        ]);

        try {
            DB::beginTransaction();

            $field = Field::find($validated['field_id']);

            if (!$field) {
                DB::rollBack();
                return back()->with('error', 'Lapangan tidak ditemukan.');
            }

            $start = new \DateTime($validated['start_time']);
            $end = new \DateTime($validated['end_time']);
            $interval = $start->diff($end);
            $hours = $interval->h + ($interval->days * 24);
            
            if ($hours < 1) {
                $hours = 1;
            }
            
            $total_price = $hours * $field->price_per_hour;

            $conflict = Booking::where('field_id', $field->id)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                          ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                          ->orWhere(function($q) use ($validated) {
                              $q->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $validated['end_time']);
                          });
                })
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return back()->with('error', 'Maaf, lapangan sudah dibooking pada waktu yang dipilih.');
            }

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'field_id' => $field->id,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'total_price' => $total_price,
                'status' => 'pending',
            ]);

            $qrData = "BOOKING-" . $booking->id . "|AMOUNT:" . $total_price . "|USER:" . auth()->user()->name;
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'qris_seabank',
                'qr_code_url' => $qrUrl,
                'status' => 'pending',
            ]);

            DB::commit();

            Log::info('Booking berhasil dibuat', [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'field_id' => $field->id,
                'total_price' => $total_price,
            ]);

            return redirect()->route('user.payment', $booking->id)
                ->with('success', 'Booking berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat booking: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat booking: ' . $e->getMessage());
        }
    }

    public function payment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        if (!$booking->payment || empty($booking->payment->qr_code_url)) {
            $qrData = "BOOKING-" . $booking->id . "|AMOUNT:" . $booking->total_price;
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
            
            if ($booking->payment) {
                $booking->payment->update(['qr_code_url' => $qrUrl]);
            }
        } else {
            $qrUrl = $booking->payment->qr_code_url;
        }

        return view('user.payment', compact('booking', 'qrUrl'));
    }

    public function checkPayment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        try {
            DB::beginTransaction();

            $booking->update(['status' => 'paid']);
            
            if ($booking->payment) {
                $booking->payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            DB::commit();

            return redirect('/')->with('success', 'Pembayaran berhasil! Booking Anda sudah dikonfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    public function uploadProof(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_notes' => 'nullable|string|max:500',
        ], [
            'payment_proof.required' => 'Bukti pembayaran harus diupload',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            if ($booking->payment) {
                if ($booking->payment->payment_proof_path && file_exists(storage_path('app/public/' . $booking->payment->payment_proof_path))) {
                    unlink(storage_path('app/public/' . $booking->payment->payment_proof_path));
                }

                $file = $request->file('payment_proof');
                $filename = 'payment_proof_' . $booking->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payment_proofs', $filename, 'public');

                $booking->payment->update([
                    'payment_proof_path' => $path,
                    'payment_proof_url' => asset('storage/' . $path),
                    'payment_notes' => $validated['payment_notes'] ?? null,
                    'proof_uploaded_at' => now(),
                    'status' => 'pending',
                ]);

                return back()->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
            }

            return back()->with('error', 'Payment record tidak ditemukan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload bukti: ' . $e->getMessage());
        }
    }
}