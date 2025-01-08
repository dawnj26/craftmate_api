<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
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

// get current user projects
Route::get('user/projects/{visibility?}', [ProjectsController::class, 'getCurrentUserProjects'])
        ->whereIn('visibility', [1, 2, 3])
        ->middleware('auth:sanctum');
Route::get('user/projects/ongoing/{visibility?}', [ProjectsController::class, 'getCurrentUserOngoingProjects'])
        ->whereIn('visibility', [1, 2, 3])
        ->middleware('auth:sanctum');
Route::get('user/projects/completed/{visibility?}', [ProjectsController::class, 'getCurrentUserCompletedProjects'])
        ->whereIn('visibility', [1, 2, 3])
        ->middleware('auth:sanctum');
Route::get('user/projects/inactive/{visibility?}', [ProjectsController::class, 'getCurrentUserInactiveProjects'])
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
Route::post('project/{project}/materials/save', [MaterialController::class, 'saveMaterialsToProject'])->where('project', '[0-9]+')->middleware('auth:sanctum');
Route::delete('project/{project}/materials/delete', [MaterialController::class, 'deleteProjectMaterials'])->where('project', '[0-9]+')->middleware('auth:sanctum');

Route::get('materials/project/{project}/used', [MaterialController::class, 'getProjectUsedMaterials'])->where('project', '[0-9]+');
Route::post('project/{project}/materials/used/add', [MaterialController::class, 'addUsedMaterialsToProject'])->where('project', '[0-9]+')->middleware('auth:sanctum');
Route::post('project/{project}/materials/used/save', [MaterialController::class, 'saveUsedMaterialsToProject'])->where('project', '[0-9]+')->middleware('auth:sanctum');
Route::delete('project/{project}/materials/used/delete', [MaterialController::class, 'deleteProjectUsedMaterials'])->where('project', '[0-9]+')->middleware('auth:sanctum');

Route::controller(AIController::class)->group(function () {
    Route::post('project/suggest', 'generateSuggestions');
    Route::post('project/generate', 'generateProject');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('user/{user}', 'getUser')->where('user', '[0-9]+')->middleware('auth:sanctum');
    Route::post('user/{user}/follow', 'toggleFollowUser')->where('user', '[0-9]+')->middleware('auth:sanctum');
    Route::get('user/profile', 'getUserProfile')->middleware('auth:sanctum');
    Route::post('user/profile', 'createProfile')->middleware('auth:sanctum');
    Route::put('user/profile', 'updateProfile')->middleware('auth:sanctum');
    Route::delete('user/profile', 'deleteProfile')->middleware('auth:sanctum');
});

Route::controller(UserController::class)->group(function() {
    Route::get('user/{user}/share', 'shareUser')->where('user', '[0-9]+');
    Route::get('user/share', 'shareCurrentUser')->middleware('auth:sanctum');
    Route::post('user/update/name', 'updateName')->middleware('auth:sanctum');
    Route::post('user/update/email', 'updateEmail')->middleware('auth:sanctum');
    Route::post('user/update/bio', 'updateBio')->middleware('auth:sanctum');
    Route::get('user/followers/{timeframe?}', 'getFollowers')->middleware('auth:sanctum');
});

Route::post('user/upload/picture', [ImageController::class, 'uploadProfilePicture'])->middleware('auth:sanctum');
Route::post('message/image/', [ImageController::class, 'uploadMessageImage'])->middleware('auth:sanctum');
Route::post('shop/images/', [ImageController::class, 'uploadMultipleImages']);
Route::post('message/video/', [VideoController::class, 'upload'])->middleware('auth:sanctum');

Route::get('shop/{id}/share', [ShopController::class, 'share']);

Route::delete('user/{user}/ban', [UserController::class, 'banUser'])->where('user', '[0-9]+')->middleware('auth:sanctum');
Route::post('user/{user}/unban', [UserController::class, 'unbanUser'])->where('user', '[0-9]+')->middleware('auth:sanctum');
