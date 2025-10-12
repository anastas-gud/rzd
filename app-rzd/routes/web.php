<?php

use Illuminate\Support\Facades\Route;

Route::get('/auth-modal', function () {
    return view('auth-modal');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/', fn() => view('home'))->name('home');

Route::get('/search-trips', [TripController::class, 'search'])->name('search-trips');
