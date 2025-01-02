<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('signup', 'signup');
        Route::get('{driver}', 'redirectDriver')->whereIn('driver', ['google', 'facebook']);
        Route::get('{driver}/callback', 'driverCallback')->whereIn('driver', ['google', 'facebook']);
        Route::post('logout', 'logout')->middleware('auth:sanctum');
    });

    Route::controller(UserController::class)->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('user/', 'user');
            Route::post('password/reset', 'resetPassword');
        });

        Route::get('user/verify', 'verifyUser');
        Route::get('users/', 'getUsers');
    });

    Route::controller(OTPController::class)->group(function () {
        Route::post('otp/send', 'sendOtp');
        Route::post('otp/verify', 'verifyOtp');
    });
});
