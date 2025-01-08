<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\StepResource;
use App\Models\Project;
use App\Models\Step;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function toggleCompleteAll(Request $request, Project $project)
    {
        $allCompleted = $project->steps->every(function ($step) {
            return $step->completed_at !== null;
        });

        $project->steps->each(function ($step) use ($allCompleted) {
            $step->completed_at = $allCompleted ? null : Carbon::now();
            $step->save();
        });

        $project->load('steps');
        return ResponseHelper::json(200, 'All steps toggled successfully');
    }

    public function toggleComplete(Request $request, Project $project, Step $step)
    {
        $step->completed_at = $step->completed_at ? null : Carbon::now();
        $step->save();

        $step->load('project');
        return ResponseHelper::json(200, 'Step toggled successfully');
    }
}
