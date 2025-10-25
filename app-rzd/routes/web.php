<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/', fn() => view('home'))->name('home');
Route::get('/search-trips', [TripController::class, 'search'])->name('search-trips');
Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips');

// Аутентификация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/bookings/active', [BookingController::class, 'active']);

    Route::post('/profile/tickets/{ticket}/cancel', [ProfileController::class, 'cancelTicket'])->name('profile.ticket.cancel');


    Route::post('/bookings/{booking}/pay', [BookingController::class, 'pay']);

});

Route::middleware(['auth', 'role:ADMIN'])->group(function () {   // future adminka
    Route::get('/admin', fn() => view('admin.dashboard'))->name('admin.dashboard');
});
