<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/', fn() => view('home'))->name('home');
Route::get('/search-trips', [TripController::class, 'search'])->name('search-trips');
Route::get('/auth-modal', function () { return view('auth-modal'); });

// Аутентификация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    // Добавьте сюда маршруты бронирования
});

Route::middleware(['auth', 'role:ADMIN'])->group(function () {   // future adminka
    Route::get('/admin', fn() => view('admin.dashboard'))->name('admin.dashboard');
});
