<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;

require __DIR__.'/user_auth/auth.php';
require __DIR__.'/project/project.php';

Route::get('projects', [ProjectsController::class, 'get']);
