<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\RateController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('/import', ImportController::class);

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/rates', [RateController::class, 'index']);

Route::apiResource('rooms', RoomController::class);
Route::apiResource('reservations', ReservationController::class);
Route::get('/rooms/{room}/availability', AvailabilityController::class);
