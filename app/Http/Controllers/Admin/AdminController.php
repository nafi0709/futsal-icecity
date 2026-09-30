<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('status', 'paid')->sum('total_price');
        $totalUsers = User::where('role', 'user')->count();
        $pendingCount = Booking::where('status', 'pending')->count();
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $todayRevenue = Booking::where('status', 'paid')->whereDate('created_at', today())->sum('total_price');

        $monthlyRevenue = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $monthlyLabels[] = $monthLabel;
            
            $revenue = Booking::where('status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_price');
            
            $monthlyRevenue[] = $revenue;
        }

        $statusData = [
            'paid' => Booking::where('status', 'paid')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        $popularFields = Field::withCount(['bookings' => function($query) {
            $query->where('status', 'paid');
        }])
        ->orderBy('bookings_count', 'desc')
        ->take(5)
        ->get();

        $dailyRevenue = [];
        $dailyLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayLabel = $date->format('d M');
            $dailyLabels[] = $dayLabel;
            
            $revenue = Booking::where('status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total_price');
            
            $dailyRevenue[] = $revenue;
        }

        $topUsers = User::withCount(['bookings' => function($query) {
            $query->where('status', 'paid');
        }])
        ->where('role', 'user')
        ->orderBy('bookings_count', 'desc')
        ->take(5)
        ->get();

        $pendingBookings = Booking::where('status', 'pending')
            ->with(['user', 'field', 'payment'])
            ->latest()
            ->paginate(10);

        $recentBookings = Booking::with(['user', 'field', 'payment'])
            ->latest()
            ->take(10)
            ->get();

        $lastMonthRevenue = Booking::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        
        $growthRate = 0;
        if ($lastMonthRevenue > 0) {
            $growthRate = (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        return view('admin.dashboard', compact(
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'pendingCount',
            'todayBookings',
            'todayRevenue',
            'monthlyRevenue',
            'monthlyLabels',
            'statusData',
            'popularFields',
            'dailyRevenue',
            'dailyLabels',
            'topUsers',
            'pendingBookings',
            'recentBookings',
            'growthRate',
            'lastMonthRevenue'
        ));
    }

    public function reviewPaymentProof($paymentId)
    {
        $payment = Payment::with(['booking.user', 'booking.field'])->findOrFail($paymentId);
        
        if (!$payment->payment_proof_url) {
            return back()->with('error', 'Belum ada bukti pembayaran yang diupload');
        }

        return view('admin.payment-review', compact('payment'));
    }

    public function approvePayment($paymentId)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($paymentId);
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);

            $payment->booking->update([
                'status' => 'paid',
            ]);

            DB::commit();

            return redirect()->route('admin.dashboard')->with('success', 'Pembayaran berhasil disetujui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }
    }

    public function rejectPayment($paymentId, Request $request)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($paymentId);
            
            $validated = $request->validate([
                'rejection_reason' => 'nullable|string|max:500',
            ]);

            $payment->update([
                'status' => 'failed',
                'payment_notes' => ($payment->payment_notes ?? '') . "\n[REJECTED] " . ($validated['rejection_reason'] ?? 'Bukti tidak valid'),
            ]);

            $payment->booking->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            return redirect()->route('admin.dashboard')->with('success', 'Pembayaran ditolak. Booking dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    public function verify(Booking $booking)
    {
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

            return back()->with('success', 'Booking #' . $booking->id . ' berhasil diverifikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

    public function cancel(Booking $booking)
    {
        try {
            DB::beginTransaction();

            $booking->update(['status' => 'cancelled']);

            if ($booking->payment) {
                $booking->payment->update(['status' => 'failed']);
            }

            DB::commit();

            return back()->with('success', 'Booking #' . $booking->id . ' dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }
}