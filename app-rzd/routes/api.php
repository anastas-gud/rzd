<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\BookingController;

Route::get('/search-trips', [TripController::class, 'search']);
Route::get('/trips/{trip}', [TripController::class, 'show']);
Route::get('/trips/{trip}/seats', [TripController::class, 'seats']);

Route::post('/bookings', [BookingController::class, 'create']);
Route::get('/bookings/{booking}/options', [BookingController::class, 'options']);
Route::post('/bookings/{booking}/options', [BookingController::class, 'applyOptions']);
Route::get('/bookings/{booking}/passengers/form', [BookingController::class, 'passengersForm']);
Route::post('/bookings/{booking}/passengers', [BookingController::class, 'updatePassengers']);
Route::get('/bookings/{booking}/summary', [BookingController::class, 'summary']);
