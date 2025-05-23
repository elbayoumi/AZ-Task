<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\AuthController;

use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum','ensure.authenticated')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Bookings
        Route::apiResource('bookings', BookingController::class);

        // Rooms (admin only logic can be added in controller)
        Route::apiResource('rooms', RoomController::class);

        // Users (admin use only)
        Route::apiResource('users', UserController::class)->only(['index', 'show']);
    });
});
