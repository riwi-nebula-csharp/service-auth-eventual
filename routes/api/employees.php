<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')
    ->middleware('is.admin')
    ->group(function () {
        Route::get('/',                [EmployeeController::class, 'index']);
        Route::get('/{id}',            [EmployeeController::class, 'show']);
        Route::post('/',               [EmployeeController::class, 'store']);
        Route::put('/{id}',            [EmployeeController::class, 'update']);
        Route::patch('/{id}/status',   [EmployeeController::class, 'updateStatus']);
        Route::delete('/{id}',         [EmployeeController::class, 'destroy']);
    });