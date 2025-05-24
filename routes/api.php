<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('v1')->middleware('accepts.json')->group(function () {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected APIs
    Route::middleware(['auth:sanctum', 'ensure.authenticated'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Bookings
        Route::apiResource('bookings', BookingController::class);

        // Rooms (admins only, handled via policies in RoomController)
        Route::apiResource('rooms', RoomController::class);

        // Users (admins only, handled via policies in UserController)
        Route::apiResource('users', UserController::class)->only(['index', 'show']);
    });
});
