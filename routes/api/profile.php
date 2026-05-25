<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')
    ->middleware('is.authenticated')
    ->group(function () {
        Route::get('/',        [ProfileController::class, 'show']);
        Route::put('/',        [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
    });