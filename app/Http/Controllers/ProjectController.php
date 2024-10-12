<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function incrementView(Project $project)
    {
        $user = auth()->user();

        if ($project->user_id !== $user->id) {
            $project->views()->firstOrCreate(['user_id' => $user->id]);
        }

        return ResponseHelper::json(200, 'View incremented');
    }

    public function createProject(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'tags' => 'nullable|string',
            'visibility' => 'required|integer',
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
            'visibility_id' => $request->input('visibility'),
        ]);

        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(' ', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        }

        $project->with('steps');

        return ResponseHelper::jsonWithData(200, 'Project created successfully', new ProjectResource($project));
    }

    public function getProject(Project $project)
    {
        // dd($project->children());
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
            'steps' => 'required',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId) {
            return ResponseHelper::errInput();
        }

        $stepsData = json_decode($request->input('steps'), true);
        $existingSteps = $project->steps()->orderBy('order')->get();

        DB::beginTransaction();
        try {
            foreach ($stepsData as $index => $stepContent) {
                $stepContentArray = json_decode($stepContent, true);
                if (isset($existingSteps[$index])) {
                    // Update existing step
                    $existingStep = $existingSteps[$index];
                    $existingStep->content = $stepContentArray;
                    $existingStep->order = $index;
                    $existingStep->save();
                } else {
                    // Create new step
                    $project->steps()->create([
                        'content' => $stepContentArray,
                        'order' => $index,
                    ]);
                }
            }

            // Delete excess steps
            if (count($existingSteps) > count($stepsData)) {
                $excessSteps = $existingSteps->slice(count($stepsData));
                foreach ($excessSteps as $excessStep) {
                    $excessStep->delete();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::json(500, 'An error occurred while updating steps');
        }

        return ResponseHelper::jsonWithData(200, 'Steps updated successfully', new ProjectResource($project));
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

    public function updateVisibility(Request $request, Project $project)
    {
        $validate = Validator::make($request->all(), [
            'visibility' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return ResponseHelper::errInput();
        }

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($user->id != $projectUserId) {
            return ResponseHelper::json(422, 'Unauthorized');
        }

        $project->visibility_id = $request->input('visibility');
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
