<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::prefix('auth')->controller(AuthenticationController::class)->group(function() {
    Route::post('login', 'login');
    Route::middleware('auth:sanctum')->post('logout', 'logout');
});


