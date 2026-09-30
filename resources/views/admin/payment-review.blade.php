@extends('layouts.admin')
@section('title', 'Verification Terminal')
@section('content')
<div class="glass-panel" style="max-width: 1100px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 30px; border-bottom: 1px solid var(--neon-dim); padding-bottom: 20px;">
        <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; font-size: 2rem; letter-spacing: 2px;">
            <i class="fa-solid fa-shield-halved"></i> PAYMENT VERIFICATION TERMINAL
        </h2>
        <p style="color: var(--ui-text);">Verifikasi keaslian bukti transfer sebelum menyetujui booking.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <!-- KOLOM KIRI: BUKTI PEMBAYARAN -->
        <div>
            <h3 style="color: var(--neon-ice); margin-bottom: 15px; font-family: 'Rajdhani', sans-serif; border-left: 4px solid var(--neon-ice); padding-left: 10px;">
                <i class="fa-solid fa-image"></i> BUKTI TRANSFER
            </h3>
            
            <div style="background: #000; padding: 10px; border-radius: 8px; border: 2px solid var(--neon-ice); box-shadow: 0 0 20px rgba(0, 240, 255, 0.2);">
                <img src="{{ $payment->payment_proof_url }}" 
                     alt="Bukti Pembayaran" 
                     style="width: 100%; max-height: 500px; object-fit: contain; display: block; border-radius: 4px;">
            </div>

            @if($payment->payment_notes)
            <div style="margin-top: 15px; padding: 15px; background: rgba(0, 240, 255, 0.05); border: 1px solid var(--neon-dim); border-radius: 4px;">
                <strong style="color: var(--neon-ice); font-size: 0.8rem;">CATATAN USER:</strong>
                <p style="color: var(--white); margin: 5px 0 0 0; font-size: 0.9rem; white-space: pre-wrap;">{{ $payment->payment_notes }}</p>
            </div>
            @endif

            <div style="margin-top: 10px; text-align: center; color: var(--ui-text); font-size: 0.8rem;">
                <i class="fa-solid fa-clock"></i> Diupload pada: {{ \Carbon\Carbon::parse($payment->proof_uploaded_at)->format('d M Y, H:i:s') }}
            </div>
        </div>

        <!-- KOLOM KANAN: DETAIL BOOKING -->
        <div>
            <h3 style="color: var(--neon-ice); margin-bottom: 15px; font-family: 'Rajdhani', sans-serif; border-left: 4px solid var(--neon-ice); padding-left: 10px;">
                <i class="fa-solid fa-file-invoice"></i> DATA BOOKING
            </h3>

            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border: 1px solid var(--neon-dim);">
                <div style="margin-bottom: 15px;">
                    <span style="color: var(--ui-text); font-size: 0.8rem;">NAMA USER</span>
                    <p style="color: var(--white); font-size: 1.1rem; font-weight: bold; margin: 0;">{{ $payment->booking->user->name }}</p>
                    <p style="color: var(--ui-text); font-size: 0.8rem; margin: 0;">{{ $payment->booking->user->email }}</p>
                </div>

                <div style="margin-bottom: 15px; border-top: 1px dashed var(--neon-dim); padding-top: 15px;">
                    <span style="color: var(--ui-text); font-size: 0.8rem;">LAPANGAN</span>
                    <p style="color: var(--white); font-size: 1.1rem; font-weight: bold; margin: 0;">{{ $payment->booking->field->name }}</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; border-top: 1px dashed var(--neon-dim); padding-top: 15px;">
                    <div>
                        <span style="color: var(--ui-text); font-size: 0.8rem;">TANGGAL</span>
                        <p style="color: var(--white); margin: 0;">{{ \Carbon\Carbon::parse($payment->booking->start_time)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <span style="color: var(--ui-text); font-size: 0.8rem;">WAKTU</span>
                        <p style="color: var(--white); margin: 0;">{{ \Carbon\Carbon::parse($payment->booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($payment->booking->end_time)->format('H:i') }}</p>
                    </div>
                </div>

                <div style="margin-bottom: 15px; border-top: 1px dashed var(--neon-dim); padding-top: 15px;">
                    <span style="color: var(--ui-text); font-size: 0.8rem;">METODE PEMBAYARAN</span>
                    <p style="color: var(--neon-ice); font-size: 1rem; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $payment->payment_method }}</p>
                </div>

                <div style="background: rgba(0, 255, 170, 0.1); padding: 15px; border-radius: 8px; border: 1px solid #00ffaa; text-align: center;">
                    <span style="color: #00ffaa; font-size: 0.8rem;">TOTAL TAGIHAN</span>
                    <h2 style="color: #00ffaa; font-size: 2rem; margin: 5px 0 0 0; font-family: 'Rajdhani', sans-serif;">
                        Rp {{ number_format($payment->booking->total_price, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}">
                    @csrf
                    <button type="submit" class="neon-btn" style="width: 100%; padding: 15px; font-size: 1rem; background: rgba(0, 255, 170, 0.1); border-color: #00ffaa; color: #00ffaa;">
                        <i class="fa-solid fa-circle-check"></i> SETUJUI
                    </button>
                </form>

                <button onclick="showRejectModal()" class="neon-btn" style="width: 100%; padding: 15px; font-size: 1rem; border-color: #ff0055; color: #ff0055;">
                    <i class="fa-solid fa-circle-xmark"></i> TOLAK
                </button>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="neon-btn" style="width: 100%; text-align: center; margin-top: 10px; padding: 10px;">
                <i class="fa-solid fa-arrow-left"></i> KEMBALI KE DASHBOARD
            </a>
        </div>
    </div>
</div>

<!-- MODAL TOLAK PEMBAYARAN -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 100; justify-content: center; align-items: center;">
    <div class="glass-panel" style="max-width: 500px; margin: 0 auto; border-color: #ff0055;">
        <h3 style="color: #ff0055; margin-bottom: 20px; font-family: 'Rajdhani', sans-serif; text-align: center;">
            <i class="fa-solid fa-triangle-exclamation"></i> KONFIRMASI PENOLAKAN
        </h3>
        <p style="color: var(--white); text-align: center; margin-bottom: 20px;">Apakah Anda yakin ingin menolak bukti ini? Booking akan otomatis dibatalkan.</p>
        
        <form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="color: var(--ui-text); display: block; margin-bottom: 10px; font-size: 0.9rem;">
                    Alasan Penolakan (Wajib)
                </label>
                <textarea name="rejection_reason" 
                          rows="3" 
                          required
                          placeholder="Contoh: Nama pengirim tidak sesuai, nominal kurang, foto buram..."
                          style="width: 100%; padding: 10px; background: rgba(255, 0, 85, 0.05); border: 1px solid #ff0055; color: var(--white); border-radius: 4px; font-family: 'Share Tech Mono', monospace;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <button type="submit" class="neon-btn" style="border-color: #ff0055; color: #ff0055; background: rgba(255,0,85,0.1);">
                    YA, TOLAK!
                </button>
                <button type="button" onclick="hideRejectModal()" class="neon-btn">
                    BATAL
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) hideRejectModal();
});
</script>
@endsection