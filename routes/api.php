<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;
use App\Models\Material;
use Illuminate\Support\Facades\Request;

require __DIR__.'/user_auth/auth.php';
require __DIR__.'/project/project.php';
require __DIR__.'/search/search.php';

Route::get('projects/latest', [ProjectsController::class, 'getLatest']);
Route::get('projects/following', [ProjectsController::class, 'getFollowing'])->middleware('auth:sanctum');
Route::get('projects/trending', [ProjectsController::class, 'getTrending']);

Route::get('project/categories', [CategoryController::class, 'getProjectCategories']);

Route::get('user/{user}/projects', [ProjectsController::class, 'getUserProjects'])
        ->where('user', '[0-9]+')
        ->middleware('auth:sanctum');


Route::get('user/projects/{visibility?}', [ProjectsController::class, 'getCurrentUserProjects'])
        ->whereIn('visibility', [1, 2, 3])
        ->middleware('auth:sanctum');

Route::delete('user/projects/delete', [ProjectsController::class, 'deleteUserProjects'])
        ->middleware('auth:sanctum');

Route::get('material-categories', [MaterialController::class, 'getMaterialCategory']);
Route::post('material/create', [MaterialController::class, 'createMaterial'])->middleware('auth:sanctum');
Route::get('materials/user', [MaterialController::class, 'getMaterials'])->middleware('auth:sanctum');
Route::get('material/{material}', [MaterialController::class, 'getMaterial'])->where('material', '[0-9]+');
Route::put('material/{material}/edit', [MaterialController::class, 'editMaterial'])->where('material', '[0-9]+')->middleware('auth:sanctum');
Route::delete('material/{material}/delete', [MaterialController::class, 'deleteMaterial'])->where('material', '[0-9]+')->middleware('auth:sanctum');
Route::delete('materials/delete', [MaterialController::class, 'deleteMaterials'])->middleware('auth:sanctum');
Route::get('materials/search', [MaterialController::class, 'searchMaterials'])->middleware('auth:sanctum');

Route::get('materials/project/{project}', [MaterialController::class, 'getProjectMaterials'])->where('project', '[0-9]+');
Route::post('project/{project}/materials/add', [MaterialController::class, 'addMaterialsToProject'])->where('project', '[0-9]+')->middleware('auth:sanctum');
Route::delete('project/{project}/materials/delete', [MaterialController::class, 'deleteProjectMaterials'])->where('project', '[0-9]+')->middleware('auth:sanctum');

Route::controller(AIController::class)->group(function () {
    Route::post('project/suggest', 'generateSuggestions');
    Route::post('project/generate', 'generateProject');
});
