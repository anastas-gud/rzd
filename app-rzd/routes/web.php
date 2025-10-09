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

Route::get('/', function () {
    return view('home');
});

Route::get('/search-trips', function () {
    return view('search-trips');
});

Route::get('/trips', function () {
    return view('trips');
});
