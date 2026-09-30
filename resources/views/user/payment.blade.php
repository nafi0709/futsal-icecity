@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
<div class="glass-panel" style="max-width: 900px; margin: 0 auto;">
    <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; text-align: center; margin-bottom: 30px;">
        <i class="fa-solid fa-credit-card"></i> PEMBAYARAN & BUKTI TRANSFER
    </h2>

    <!-- Booking Info -->
    <div style="background: rgba(0, 240, 255, 0.05); border: 1px solid var(--neon-dim); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: var(--white); margin-bottom: 15px; font-family: 'Rajdhani', sans-serif;">DETAIL BOOKING</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.9rem;">
            <div>
                <span style="color: var(--ui-text);">Lapangan:</span><br>
                <strong style="color: var(--white);">{{ $booking->field->name }}</strong>
            </div>
            <div>
                <span style="color: var(--ui-text);">Tanggal:</span><br>
                <strong style="color: var(--white);">{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</strong>
            </div>
            <div>
                <span style="color: var(--ui-text);">Waktu:</span><br>
                <strong style="color: var(--white);">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </strong>
            </div>
            <div>
                <span style="color: var(--ui-text);">Durasi:</span><br>
                <strong style="color: var(--white);">
                    {{ \Carbon\Carbon::parse($booking->start_time)->diffInHours($booking->end_time) }} Jam
                </strong>
            </div>
            <div>
                <span style="color: var(--ui-text);">Status:</span><br>
                @if($booking->status === 'paid')
                    <strong style="color: #00ffaa;">✓ PAID</strong>
                @elseif($booking->status === 'pending')
                    <strong style="color: #ffaa00;"> PENDING</strong>
                @else
                    <strong style="color: #ff0055;">✗ CANCELLED</strong>
                @endif
            </div>
        </div>
    </div>

    @if($booking->status !== 'paid')
    <!-- Payment Methods -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-wallet"></i> PILIH METODE PEMBAYARAN
        </h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div onclick="selectMethod('qris')" id="btn-qris" 
                 style="border: 2px solid var(--neon-ice); background: rgba(0, 240, 255, 0.1); 
                        padding: 20px; border-radius: 8px; cursor: pointer; text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-qrcode" style="font-size: 2.5rem; color: var(--neon-ice); margin-bottom: 10px;"></i>
                <h4 style="color: var(--white);">QRIS</h4>
                <p style="font-size: 0.8rem; color: var(--ui-text);">Semua e-wallet & mobile banking</p>
            </div>

            <div onclick="selectMethod('seabank')" id="btn-seabank"
                 style="border: 2px solid var(--neon-dim); background: rgba(0, 240, 255, 0.05); 
                        padding: 20px; border-radius: 8px; cursor: pointer; text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-building-columns" style="font-size: 2.5rem; color: var(--neon-ice); margin-bottom: 10px;"></i>
                <h4 style="color: var(--white);">SeaBank</h4>
                <p style="font-size: 0.8rem; color: var(--ui-text);">Transfer langsung SeaBank</p>
            </div>

            <div onclick="selectMethod('gopay')" id="btn-gopay"
                 style="border: 2px solid var(--neon-dim); background: rgba(0, 240, 255, 0.05); 
                        padding: 20px; border-radius: 8px; cursor: pointer; text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-wallet" style="font-size: 2.5rem; color: var(--neon-ice); margin-bottom: 10px;"></i>
                <h4 style="color: var(--white);">GoPay</h4>
                <p style="font-size: 0.8rem; color: var(--ui-text);">E-wallet GoPay</p>
            </div>

            <div onclick="selectMethod('transfer')" id="btn-transfer"
                 style="border: 2px solid var(--neon-dim); background: rgba(0, 240, 255, 0.05); 
                        padding: 20px; border-radius: 8px; cursor: pointer; text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-money-bill-transfer" style="font-size: 2.5rem; color: var(--neon-ice); margin-bottom: 10px;"></i>
                <h4 style="color: var(--white);">Transfer Bank</h4>
                <p style="font-size: 0.8rem; color: var(--ui-text);">Transfer manual</p>
            </div>
        </div>
    </div>

    <!-- QR Code Display -->
    <div id="qr-section" style="display: none; text-align: center; padding: 30px; background: white; 
         border-radius: 12px; margin-bottom: 30px;">
        <h3 style="color: #000; margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            SCAN QR CODE ATAU TRANSFER KE
        </h3>
        
        @if($qrUrl)
        <img src="{{ $qrUrl }}" 
             alt="QR Code Payment" 
             style="width: 300px; height: 300px; border: 3px solid var(--neon-ice); border-radius: 8px;">
        @endif
        
        <div style="margin-top: 20px; padding: 15px; background: rgba(0, 240, 255, 0.1); border-radius: 8px;">
            <p style="color: #333; font-weight: bold; margin-bottom: 5px;">Nominal Transfer:</p>
            <p style="color: #000; font-size: 1.5rem; font-weight: bold;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <div style="margin-top: 20px; text-align: left; padding: 15px; background: rgba(0,0,0,0.05); border-radius: 8px;">
            <p style="color: #333; margin: 5px 0;"><strong>Bank Tujuan:</strong> SeaBank Indonesia</p>
            <p style="color: #333; margin: 5px 0;"><strong>No. Rekening:</strong> 9876543210</p>
            <p style="color: #333; margin: 5px 0;"><strong>Atas Nama:</strong> PT Futsal Ice City</p>
        </div>
    </div>

    <!-- Upload Bukti -->
    <div class="glass-panel" style="border: 2px solid var(--neon-ice); margin-bottom: 30px;">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-upload"></i> UPLOAD BUKTI PEMBAYARAN
        </h3>
        
        <form method="POST" action="{{ route('user.payment.upload', $booking->id) }}" enctype="multipart/form-data">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="color: var(--white); display: block; margin-bottom: 10px; font-weight: bold;">
                    Upload Bukti Transfer (Screenshot/Foto)
                </label>
                <input type="file" 
                       name="payment_proof" 
                       id="payment_proof" 
                       accept="image/*"
                       style="width: 100%; padding: 10px; background: rgba(0, 240, 255, 0.05); border: 1px solid var(--neon-dim); color: var(--white); border-radius: 4px;"
                       onchange="previewImage(this)"
                       required>
                <small style="color: var(--ui-text); display: block; margin-top: 5px;">
                    Format: JPG, PNG, GIF (Max 2MB)
                </small>
            </div>

            <div id="image_preview" style="display: none; margin-bottom: 20px; text-align: center;">
                <img id="preview_img" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border: 2px solid var(--neon-ice); border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: var(--white); display: block; margin-bottom: 10px; font-weight: bold;">
                    Catatan (Opsional)
                </label>
                <textarea name="payment_notes" 
                          rows="3" 
                          placeholder="Contoh: Transfer dari BCA ke SeaBank..."
                          style="width: 100%; padding: 10px; background: rgba(0, 240, 255, 0.05); border: 1px solid var(--neon-dim); color: var(--white); border-radius: 4px; font-family: 'Share Tech Mono', monospace;">{{ $booking->payment?->payment_notes ?? '' }}</textarea>
            </div>

            <button type="submit" class="neon-btn" style="width: 100%; font-size: 1.1rem;">
                <i class="fa-solid fa-upload"></i> UPLOAD BUKTI PEMBAYARAN
            </button>
        </form>
    </div>

    @if($booking->payment?->payment_proof_url)
    <div style="background: rgba(0, 255, 170, 0.05); border: 1px solid #00ffaa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: #00ffaa; margin-bottom: 15px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-check-circle"></i> BUKTI PEMBAYARAN TERKIRIM
        </h3>
        <div style="text-align: center;">
            <img src="{{ $booking->payment->payment_proof_url }}" 
                 alt="Bukti Pembayaran" 
                 style="max-width: 100%; max-height: 400px; border: 3px solid #00ffaa; border-radius: 8px;">
        </div>
        @if($booking->payment->payment_notes)
        <div style="margin-top: 15px; padding: 10px; background: rgba(0,0,0,0.3); border-radius: 4px;">
            <strong style="color: #00ffaa;">Catatan:</strong>
            <p style="color: var(--white); margin: 5px 0 0 0;">{{ $booking->payment->payment_notes }}</p>
        </div>
        @endif
        <p style="color: var(--ui-text); margin-top: 10px; text-align: center;">
            <i class="fa-solid fa-clock"></i> 
            Diupload: {{ \Carbon\Carbon::parse($booking->payment->proof_uploaded_at)->format('d M Y H:i') }}
        </p>
    </div>
    @endif
    @endif

    <!-- Total -->
    <div style="background: rgba(0, 240, 255, 0.1); border: 2px solid var(--neon-ice); 
         padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px;">
        <p style="color: var(--ui-text); margin-bottom: 5px;">TOTAL PEMBAYARAN</p>
        <h2 style="color: var(--neon-ice); font-size: 2.5rem; font-weight: bold;">
            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
        </h2>
    </div>

    <!-- Buttons -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <a href="/" class="neon-btn" style="width: 100%; text-align: center; font-size: 1.1rem;">
            <i class="fa-solid fa-home"></i> KEMBALI
        </a>
        @if($booking->status === 'pending')
        <a href="{{ route('user.dashboard') }}" class="neon-btn" style="width: 100%; text-align: center; font-size: 1.1rem; border-color: #ffaa00; color: #ffaa00;">
            <i class="fa-solid fa-clock"></i> CEK STATUS
        </a>
        @endif
    </div>
</div>

<script>
function selectMethod(method) {
    document.getElementById('btn-qris').style.borderColor = 'var(--neon-dim)';
    document.getElementById('btn-qris').style.background = 'rgba(0, 240, 255, 0.05)';
    document.getElementById('btn-seabank').style.borderColor = 'var(--neon-dim)';
    document.getElementById('btn-seabank').style.background = 'rgba(0, 240, 255, 0.05)';
    document.getElementById('btn-gopay').style.borderColor = 'var(--neon-dim)';
    document.getElementById('btn-gopay').style.background = 'rgba(0, 240, 255, 0.05)';
    document.getElementById('btn-transfer').style.borderColor = 'var(--neon-dim)';
    document.getElementById('btn-transfer').style.background = 'rgba(0, 240, 255, 0.05)';
    
    document.getElementById('btn-' + method).style.borderColor = 'var(--neon-ice)';
    document.getElementById('btn-' + method).style.background = 'rgba(0, 240, 255, 0.1)';
    
    document.getElementById('qr-section').style.display = 'block';
    document.getElementById('qr-section').scrollIntoView({ behavior: 'smooth' });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_img').src = e.target.result;
            document.getElementById('image_preview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection