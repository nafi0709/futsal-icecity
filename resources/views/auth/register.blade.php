@extends('layouts.app')
@section('title', 'Register - Ice City')
@section('content')
<div class="glass-panel" style="max-width: 400px; margin: 50px auto;">
    <h2 style="color: var(--neon-ice); text-align: center; margin-bottom: 20px; font-family: 'Rajdhani', sans-serif;">NEW USER REGISTRATION</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="name" class="neon-input" placeholder="FULL NAME" required>
        <input type="email" name="email" class="neon-input" placeholder="EMAIL ADDRESS" required>
        <input type="password" name="password" class="neon-input" placeholder="PASSWORD (min 6 chars)" required>
        <button type="submit" class="neon-btn" style="width: 100%; margin-top: 10px;">INITIATE REGISTRATION</button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 0.8rem;">
        Sudah punya akses? <a href="{{ route('login') }}" style="color: var(--neon-ice);">Login di sini</a>
    </p>
</div>
@endsection