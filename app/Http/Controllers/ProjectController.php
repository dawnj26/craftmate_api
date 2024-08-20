<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function createProject(Request $request) {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
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

        $description = $request->has('description') ? $request->input('description') : null;

        if ($description != null) {
            $description = json_decode($description, true);
        }

        $project = Project::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'description' => $description,
        ]);

        if ($request->has('tags'))
        {
            $tagNames = array_map('trim', explode(' ', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        }

        return response()->json([
            'metadata' => [
                'status' => 201,
                'message' => 'Project created successfully',
            ],
        ],201);
    }
}
