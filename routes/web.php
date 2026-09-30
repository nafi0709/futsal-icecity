<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\Admin\AdminController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Routes
    Route::get('/', [BookingController::class, 'index'])->name('user.dashboard');
    Route::post('/booking', [BookingController::class, 'store'])->name('user.booking.store');
    Route::get('/payment/{booking}', [BookingController::class, 'payment'])->name('user.payment');
    Route::post('/payment/{booking}/check', [BookingController::class, 'checkPayment'])->name('user.payment.check');
    Route::post('/payment/{booking}/upload', [BookingController::class, 'uploadProof'])->name('user.payment.upload');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/payments/{payment}/review', [AdminController::class, 'reviewPaymentProof'])->name('payment.review');
    Route::post('/payments/{payment}/approve', [AdminController::class, 'approvePayment'])->name('payment.approve');
    Route::post('/payments/{payment}/reject', [AdminController::class, 'rejectPayment'])->name('payment.reject');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/booking/{booking}/verify', [AdminController::class, 'verify'])->name('booking.verify');
    Route::post('/booking/{booking}/cancel', [AdminController::class, 'cancel'])->name('booking.cancel');
});