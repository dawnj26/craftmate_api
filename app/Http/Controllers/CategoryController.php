<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectCategoryResource;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getProjectCategories()
    {
        $categories = ProjectCategory::all();
        return ProjectCategoryResource::collection($categories);
    }
}
