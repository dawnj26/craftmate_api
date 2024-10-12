<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;

require __DIR__.'/user_auth/auth.php';
require __DIR__.'/project/project.php';

Route::get('projects/latest', [ProjectsController::class, 'getLatest']);

Route::get('user/{user}/projects', [ProjectsController::class, 'getUserProjects'])
        ->where('user', '[0-9]+')
        ->middleware('auth:sanctum');


Route::get('user/projects/{visibility?}', [ProjectsController::class, 'getCurrentUserProjects'])
        ->whereIn('visibility', [1, 2, 3])
        ->middleware('auth:sanctum');

Route::delete('user/projects/delete', [ProjectsController::class, 'deleteUserProjects'])
        ->middleware('auth:sanctum');
