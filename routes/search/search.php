<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::controller(SearchController::class)->group(function () {
    Route::get('search/suggest', 'searchSuggest');
    Route::get('search/projects', 'searchProjects');
    Route::get('search/users', 'searchUsers');
});
