<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function get() {
        $projects = Project::where('is_public', 1)->latest()->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved',  new ProjectCollection($projects));
    }
}
