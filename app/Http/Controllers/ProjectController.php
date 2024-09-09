<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function createProject(Request $request) {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'tags' => 'nullable|string',
            'is_public' => 'required|boolean',
        ]);

        $user = auth()->user();

        if (!$user)
        {
            return response()->json([
                'metadata'=> [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ]);
        }

        if ($validate->fails())
        {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields or validation error',
                ],
            ], 422);
        }

        $project = Project::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'is_public' => $request->input('is_public'),
        ]);

        if ($request->has('tags'))
        {
            $tagNames = array_map('trim', explode(' ', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        }

        $project->with(['steps' => function ($query) {
            $query->whereNull('parent_id');
        }]);

        return response()->json([
            'metadata' => [
                'status' => 201,
                'message' => 'Project created successfully',
            ],
            'data' => new ProjectResource($project),
        ],201);
    }

    public function getProject(Project $project)
    {
        return response()->json([
            "metadata" => [
                'status' => 200,
                'message' => 'Project retrieved successfully',
            ],
            'data' => new ProjectResource($project),
        ]);
    }

    public function updateDescription(Request $request,Project $project)
    {
        $validate = Validator::make($request->all(), [
            'description' => 'required|json',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId)
        {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields or validation error',
                ],
            ], 422);
        }

        $project->description = $request->input('description');
        $project->save();

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Updated successfully',
            ],
        ], 200);
    }

    public function updateSteps(Request $request, Project $project)
    {
        $validate = Validator::make($request->all(), [
            'steps' => 'required|json',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId)
        {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields or validation error',
                ],
            ], 422);
        }

        $step = $project->step;

        $step->content = $request->input('steps');
        $step->save();

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Updated successfully',
            ],
        ], 200);
    }
}
