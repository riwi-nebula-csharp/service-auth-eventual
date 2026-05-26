<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    // Públicas
    Route::post('/register',          [AuthController::class, 'register']);
    Route::post('/login',             [AuthController::class, 'login']);
    Route::post('/logout',            [AuthController::class, 'logout']);
    Route::post('/password/forgot',   [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset',    [AuthController::class, 'resetPassword']);

    // Google OAuth
    Route::get('/google/redirect',    [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback',    [AuthController::class, 'handleGoogleCallback']);
});