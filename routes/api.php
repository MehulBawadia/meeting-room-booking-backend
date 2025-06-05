<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::get('/meeting-rooms/available', [RoomAvailabilityController::class, 'index']);

    Route::get('/meeting-rooms/my-bookings', [BookingController::class, 'index']);
    Route::post('/meeting-rooms/book', [BookingController::class, 'store']);
    Route::get('/meeting-rooms/daily-stats', [BookingController::class, 'myDailyStats']);

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions/buy', [SubscriptionController::class, 'buy']);
});
