<?php
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StepController;

Route::prefix('project')->group(function () {
    Route::controller(ProjectController::class)->group(function () {
        //create
        Route::post('create', 'createProject')->middleware('auth:sanctum');
        Route::post('{project}/fork', 'fork')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('/suggestion/save', 'saveSuggestion')->middleware('auth:sanctum');
        Route::post('{project}/start', 'start')->where('project', '[0-9]+')->middleware('auth:sanctum');

        // put
        Route::put('{project}/finish', 'finish')->where('project', '[0-9]+')->middleware('auth:sanctum');

        // edit
        Route::post('{project}/edit/description', 'updateDescription')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/edit/steps', 'updateSteps')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/edit', 'update')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/edit/visibility', 'updateVisibility')->where('project', '[0-9]+')->middleware('auth:sanctum');

        // get
        Route::get('{project}', 'getProject')->where('project', '[0-9]+');
        Route::get('{project}/share', 'share')->where('project', '[0-9]+')->middleware('auth:sanctum');

        // delete
        Route::delete('{project}/delete', 'delete')->where('project', '[0-9]+')->middleware('auth:sanctum');
    });

    Route::post('{project}/like', [LikeController::class, 'toggle'])->middleware('auth:sanctum');

    // Media uploading
    Route::post('image/upload', [ImageController::class, 'uploadDocumentImage']);
    Route::post('{project}/image/upload', [ImageController::class, 'uploadProjectImage'])->where('project', '[0-9]+')->middleware('auth:sanctum');
    Route::post('video/upload', [VideoController::class, 'upload']);

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::get('{project}/comments', 'getComments')->where('project', '[0-9]+');
        Route::post('{project}/comment/{comment}/like', 'toggleLike')->where('comment', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/create', 'createComment')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/{comment}/edit', 'update')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::post('{project}/comment/{comment}/reply', 'reply')->where('project', '[0-9]+')->middleware('auth:sanctum');
        Route::delete('comment/{comment}/delete', 'delete')->where('project', '[0-9]+')->middleware('auth:sanctum');
    });

    Route::put('{project}/step/{step}/toggle-complete', [StepController::class, 'toggleComplete'])->where('project', '[0-9]+')->where('step', '[0-9]+')->middleware('auth:sanctum');
    Route::put('{project}/steps/toggle-complete', [StepController::class, 'toggleCompleteAll'])->where('project', '[0-9]+')->middleware('auth:sanctum');
});


