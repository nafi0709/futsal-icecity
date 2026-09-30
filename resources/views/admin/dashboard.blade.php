@extends('layouts.admin')
@section('title', 'Admin Command Center')
@section('content')
<div class="glass-panel" style="text-align: center; border-color: var(--neon-ice);">
    <h1 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; font-size: 2.5rem; letter-spacing: 3px;">
        <i class="fa-solid fa-satellite-dish"></i> COMMAND CENTER ACTIVE
    </h1>
    <p style="color: var(--ui-text); margin-top: 10px;">Welcome back, Commander {{ Auth::user()->name }}. All systems operational.</p>
</div>

<!-- STATISTIK CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="glass-panel" style="text-align: center;">
        <h3 style="color: var(--ui-text); font-size: 0.9rem; text-transform: uppercase;">Total Bookings</h3>
        <p style="font-size: 3rem; color: var(--neon-ice); font-weight: bold;">{{ $totalBookings }}</p>
        <p style="font-size: 0.7rem; color: var(--ui-text);">MODULES DEPLOYED</p>
    </div>
    <div class="glass-panel" style="text-align: center;">
        <h3 style="color: var(--ui-text); font-size: 0.9rem; text-transform: uppercase;">Total Revenue</h3>
        <p style="font-size: 2rem; color: #00ffaa; font-weight: bold;">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
        <p style="font-size: 0.7rem; color: var(--ui-text);">CREDITS ACQUIRED</p>
    </div>
    <div class="glass-panel" style="text-align: center;">
        <h3 style="color: var(--ui-text); font-size: 0.9rem; text-transform: uppercase;">Today</h3>
        <p style="font-size: 2rem; color: var(--neon-ice); font-weight: bold;">{{ $todayBookings }}</p>
        <p style="font-size: 0.7rem; color: var(--ui-text);">BOOKINGS TODAY</p>
    </div>
    <div class="glass-panel" style="text-align: center;">
        <h3 style="color: var(--ui-text); font-size: 0.9rem; text-transform: uppercase;">Pending</h3>
        <p style="font-size: 3rem; color: #ffaa00; font-weight: bold;">{{ $pendingCount ?? 0 }}</p>
        <p style="font-size: 0.7rem; color: var(--ui-text);">AWAITING VERIFICATION</p>
    </div>
</div>

<!-- CHARTS SECTION -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="glass-panel">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-chart-line"></i> PENDAPATAN 6 BULAN
        </h3>
        <canvas id="revenueChart" height="250"></canvas>
    </div>
    <div class="glass-panel">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-chart-pie"></i> STATUS BOOKING
        </h3>
        <canvas id="statusChart" height="250"></canvas>
    </div>
    <div class="glass-panel">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-chart-bar"></i> PENDAPATAN 7 HARI
        </h3>
        <canvas id="dailyChart" height="250"></canvas>
    </div>
    <div class="glass-panel">
        <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
            <i class="fa-solid fa-trophy"></i> LAPANGAN POPULER
        </h3>
        <canvas id="fieldsChart" height="250"></canvas>
    </div>
</div>

<!-- TOP USERS -->
@if(isset($topUsers) && $topUsers->count() > 0)
<div class="glass-panel" style="margin-bottom: 30px;">
    <h3 style="color: var(--neon-ice); margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">
        <i class="fa-solid fa-users"></i> TOP 5 PELANGGAN SETIA
    </h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Total Booking</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topUsers as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td style="color: var(--neon-ice); font-weight: bold;">{{ $user->bookings_count }}x</td>
                <td style="color: #00ffaa;">
                    Rp {{ number_format($user->bookings()->where('status', 'paid')->sum('total_price'), 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- PENDING VERIFICATION TABLE -->
<div class="glass-panel">
    <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; margin-bottom: 20px; letter-spacing: 2px;">
        <i class="fa-solid fa-hourglass-half"></i> PENDING VERIFICATION QUEUE
    </h2>
    
    @if($pendingBookings->count() > 0)
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Lapangan</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Status Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingBookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name ?? 'Unknown' }}</td>
                    <td>{{ $booking->field->name ?? 'Unknown' }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M H:i') }}</td>
                    <td style="color: var(--white); font-weight: bold;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span style="color: var(--neon-ice);">{{ strtoupper($booking->payment?->payment_method ?? 'N/A') }}</span>
                        <br>
                        @if($booking->payment?->payment_proof_url)
                            <span style="color: #00ffaa; font-size: 0.7rem;"><i class="fa-solid fa-image"></i> Bukti Terupload</span>
                        @else
                            <span style="color: #ffaa00; font-size: 0.7rem;"><i class="fa-solid fa-clock"></i> Menunggu Bukti</span>
                        @endif
                    </td>
                    <td>
                        @if($booking->payment?->payment_proof_url)
                            <a href="{{ route('admin.payment.review', $booking->payment->id) }}" 
                               class="neon-btn" 
                               style="padding: 5px 10px; font-size: 0.7rem; display: inline-block; margin-bottom: 5px; background: rgba(0, 240, 255, 0.1);">
                                <i class="fa-solid fa-eye"></i> CEK BUKTI
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.booking.verify', $booking->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="neon-btn" style="padding: 5px 10px; font-size: 0.7rem; border-color: #00ffaa; color: #00ffaa;">
                                <i class="fa-solid fa-check"></i> SKIP VERIFY
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: var(--ui-text);">
        <i class="fa-solid fa-circle-check" style="color: #00ffaa; font-size: 3rem;"></i>
        <p style="margin-top: 15px; font-size: 1.1rem;">ALL CLEAR - NO PENDING DATA</p>
    </div>
    @endif
</div>

<!-- RECENT BOOKINGS -->
@if(isset($recentBookings) && $recentBookings->count() > 0)
<div class="glass-panel">
    <h2 style="color: var(--neon-ice); font-family: 'Rajdhani', sans-serif; margin-bottom: 20px; letter-spacing: 2px;">
        <i class="fa-solid fa-clock-rotate-left"></i> RECENT ACTIVITY
    </h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Lapangan</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentBookings as $booking)
            <tr>
                <td>#{{ $booking->id }}</td>
                <td>{{ $booking->user->name ?? 'Unknown' }}</td>
                <td>{{ $booking->field->name ?? 'Unknown' }}</td>
                <td>
                    @if($booking->status === 'paid')
                        <span style="color: #00ffaa;">PAID</span>
                    @elseif($booking->status === 'pending')
                        <span style="color: #ffaa00;">PENDING</span>
                    @else
                        <span style="color: #ff0055;">CANCELLED</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- CHART.JS SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart 1: Monthly Revenue
const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
new Chart(ctxRevenue, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyLabels) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode($monthlyRevenue) !!},
            borderColor: '#00F0FF',
            backgroundColor: 'rgba(0, 240, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#8aaeb5', callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' },
                grid: { color: 'rgba(0, 240, 255, 0.1)' }
            },
            x: { ticks: { color: '#8aaeb5' }, grid: { color: 'rgba(0, 240, 255, 0.1)' } }
        },
        plugins: { legend: { labels: { color: '#8aaeb5' } } }
    }
});

// Chart 2: Booking Status
const ctxStatus = document.getElementById('statusChart').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Pending', 'Cancelled'],
        datasets: [{
            data: [{{ $statusData['paid'] ?? 0 }}, {{ $statusData['pending'] ?? 0 }}, {{ $statusData['cancelled'] ?? 0 }}],
            backgroundColor: ['#00ffaa', '#ffaa00', '#ff0055'],
            borderColor: '#0c1016',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { color: '#8aaeb5' } } }
    }
});

// Chart 3: Daily Revenue
const ctxDaily = document.getElementById('dailyChart').getContext('2d');
new Chart(ctxDaily, {
    type: 'bar',
    data: {
        labels: {!! json_encode($dailyLabels) !!},
        datasets: [{
            label: 'Pendapatan Harian',
            data: {!! json_encode($dailyRevenue) !!},
            backgroundColor: 'rgba(0, 240, 255, 0.6)',
            borderColor: '#00F0FF',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { color: '#8aaeb5' }, grid: { color: 'rgba(0, 240, 255, 0.1)' } },
            x: { ticks: { color: '#8aaeb5' }, grid: { display: false } }
        },
        plugins: { legend: { labels: { color: '#8aaeb5' } } }
    }
});

// Chart 4: Popular Fields
const ctxFields = document.getElementById('fieldsChart').getContext('2d');
new Chart(ctxFields, {
    type: 'bar',
    data: {
        labels: {!! json_encode($popularFields->pluck('name')) !!},
        datasets: [{
            label: 'Jumlah Booking',
            data: {!! json_encode($popularFields->pluck('bookings_count')) !!},
            backgroundColor: 'rgba(0, 255, 170, 0.6)',
            borderColor: '#00ffaa',
            borderWidth: 2,
            indexAxis: 'y'
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { beginAtZero: true, ticks: { color: '#8aaeb5' }, grid: { color: 'rgba(0, 240, 255, 0.1)' } },
            y: { ticks: { color: '#8aaeb5' }, grid: { display: false } }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@endsection