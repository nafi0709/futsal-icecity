@extends('layouts.app')
@section('title', 'User Dashboard')
@section('content')
<div class="glass-panel">
    <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; margin-bottom: 20px;">AVAILABLE MODULES (LAPANGAN)</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach($fields as $field)
        <div style="border: 1px solid var(--neon-dim); padding: 20px; border-radius: 4px;">
            <h3 style="color: var(--white);">{{ $field->name }}</h3>
            <p style="font-size: 0.9rem; margin: 10px 0;">{{ $field->description }}</p>
            <p style="color: var(--neon-ice); font-size: 1.2rem; font-weight: bold;">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }} / JAM</p>
            <form method="POST" action="{{ route('user.booking.store') }}" style="margin-top: 15px;">
                @csrf
                <input type="hidden" name="field_id" value="{{ $field->id }}">
                <input type="datetime-local" name="start_time" class="neon-input" required>
                <input type="datetime-local" name="end_time" class="neon-input" required>
                <button type="submit" class="neon-btn" style="width: 100%;">BOOKING NOW</button>
            </form>
        </div>
        @endforeach
    </div>
</div>

<div class="glass-panel">
    <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; margin-bottom: 20px;">BOOKING LOGS</h2>
    <table class="data-table">
        <thead><tr><th>Lapangan</th><th>Waktu</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->field->name }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                <td class="status-{{ $booking->status }}">{{ strtoupper($booking->status) }}</td>
                <td>
                    @if($booking->status === 'pending')
                        <a href="{{ route('user.payment', $booking->id) }}" class="neon-btn" style="padding: 5px 10px; font-size: 0.8rem;">BAYAR</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection