<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function createProject(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'tags' => 'nullable|string',
            'is_public' => 'required|boolean',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ]);
        }

        if ($validate->fails()) {
            return ResponseHelper::errInput();
        }

        $project = Project::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'is_public' => $request->input('is_public'),
        ]);

        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(' ', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        }

        $project->with(['steps' => function ($query) {
            $query->whereNull('parent_id');
        }]);

        return ResponseHelper::jsonWithData(200, 'Project created successfully', new ProjectResource($project));
    }

    public function getProject(Project $project)
    {
        return ResponseHelper::jsonWithData(200, 'Project fetched successfully', new ProjectResource($project));
    }

    public function updateDescription(Request $request, Project $project)
    {
        $validate = Validator::make($request->all(), [
            'description' => 'required|json',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId) {
            return ResponseHelper::errInput();
        }

        $project->description = $request->input('description');
        $project->save();

        return ResponseHelper::json(200, 'Updated successfully');
    }

    public function updateSteps(Request $request, Project $project)
    {
        $validate = Validator::make($request->all(), [
            'steps' => 'required|json',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId) {
            return ResponseHelper::errInput();
        }

        $step = $project->step;

        $step->content = $request->input('steps');
        $step->save();

        return ResponseHelper::json(200, 'Updated successfully');
    }

    public function update(Request $request, Project $project)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'tags' => 'nullable|string',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId) {
            return ResponseHelper::errInput();
        }

        $project->title = $request->input('title');
        $project->save();

        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(' ', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        } else {
            $project->tags()->sync([]);
        }

        return ResponseHelper::jsonWithData(200, 'Project created successfully', new ProjectResource($project));
    }

    public function updateVisibility(Project $project)
    {
        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($user->id != $projectUserId) {
            return ResponseHelper::json(422, 'Missing required fields or validation error');
        }

        $project->is_public = !$project->is_public;
        $project->save();

        return ResponseHelper::json(200, 'Updated successfully');
    }

    public function delete(Project $project)
    {
        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($user->id != $projectUserId) {
            return ResponseHelper::json(401, 'Unauthorized');
        }

        $project->delete();

        return ResponseHelper::json(200, 'Deleted successfully');
    }
}
