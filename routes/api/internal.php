<?php

use App\Http\Controllers\InternalController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal')
    ->middleware('is.internal')
    ->group(function () {
        Route::get('/users/by-email', [InternalController::class, 'getUserByEmail']);
        Route::get('/users/emails',   [InternalController::class, 'getUserEmailsByIds']);
    });