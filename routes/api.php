<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::prefix('auth')->controller(AuthenticationController::class)->group(function() {
    Route::post('login', 'login');
    Route::post('signup', 'signup');
    Route::get('{driver}', 'redirectDriver')->whereIn('driver', ['google', 'facebook']);
    Route::get('{driver}/callback', 'driverCallback')->whereIn('driver', ['google', 'facebook']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('logout', 'logout');
        Route::get('user/{id?}', 'user');
    });

    Route::post('otp/send', 'sendOtp');
});


