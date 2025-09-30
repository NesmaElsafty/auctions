<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgencyController;

Route::match(['GET', 'POST'], '/ping', function () {
    return ['ok' => true];
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/uploadAvatar', [AuthController::class, 'uploadAvatar']);
    Route::delete('/deleteAvatar', [AuthController::class, 'deleteAvatar']);

    Route::apiResource('agencies', AgencyController::class);
});