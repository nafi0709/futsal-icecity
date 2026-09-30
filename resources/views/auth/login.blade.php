@extends('layouts.app')
@section('title', 'Login - Ice City')
@section('content')
<div class="glass-panel" style="max-width: 400px; margin: 50px auto;">
    <h2 style="color: var(--neon-ice); text-align: center; margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">SYSTEM ACCESS</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" class="neon-input" placeholder="EMAIL ADDRESS" required>
        <input type="password" name="password" class="neon-input" placeholder="PASSWORD" required>
        <button type="submit" class="neon-btn" style="width: 100%; margin-top: 10px;">INITIATE LOGIN</button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 0.8rem;">
        Belum punya akses? <a href="{{ route('register') }}" style="color: var(--neon-ice);">Daftar di sini</a>
    </p>
</div>
@endsection