<?php

use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CommentController;

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
    });

    Route::controller(OTPController::class)->group(function () {
        Route::post('otp/send', 'sendOtp');
        Route::post('otp/verify', 'verifyOtp');
    });
});

Route::prefix('project')->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        Route::post('create', 'createProject')->middleware('auth:sanctum');
        Route::post('{project}/edit/description', 'updateDescription')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/edit/steps', 'updateSteps')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::get('{project}', 'getProject')->where('project', '[0-9]+');
    });

    Route::post('{project}/like', [LikeController::class, 'toggle'])->middleware('auth:sanctum');

    // Media uploading
    Route::post('image/upload', [ImageController::class, 'uploadDocumentImage']);
    Route::post('video/upload', [VideoController::class, 'upload']);

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::get('{project}/comments', 'getComments')->where('project', '[0-9]+');
        Route::post('{project}/comment/{comment}/like', 'toggleLike')->where('comment', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/create', 'createComment')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/{comment}/edit', 'update')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::delete('{project}/comment/{comment}/delete', 'delete')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/{comment}/toggle-like', 'toggleLike')->where('comment', '[0-9]+')->middleware('auth:sanctum');
    });
});
