<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Material;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function share(Request $request, Project $project)
    {
        return ResponseHelper::jsonWithData(200, 'Project fetched successfully', [
            'share_link' => URL::to("/project/{$project->id}")
        ]);
    }

    public function finish(Request $request, Project $project)
    {
        $project->completed_at = Carbon::now();
        $project->save();

        $project->load(['materials', 'steps', 'projectCategory']);
        return ResponseHelper::jsonWithData(200, 'Finished successfully', new ProjectResource($project));
    }

    public function start(Request $request, Project $project)
    {
        $project->started_at = Carbon::now();
        $project->save();

        $project->load(['materials', 'steps', 'projectCategory']);
        return ResponseHelper::jsonWithData(200, 'Started successfully', new ProjectResource($project));
    }

    public function saveSuggestion(Request $request)
    {
        // 13 default category
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'steps' => 'required|array',
            'steps.*' => 'string',
            'materials' => 'required|array',
            'materials.*' => 'string',
        ]);

        $user = auth()->user();
        $description = $request->description;
        try {
            DB::beginTransaction();
            $project = Project::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'description' => [['insert' => "$description\n"]],
                'visibility_id' => 2,
                'project_category_id' => 51,
            ]);
            foreach ($request->materials as $material) {
                $project->materials()->create([
                    'name' => $material,
                    'category_id' => 13,
                    'quantity' => 1,
                    'user_id' => $user->id,
                ]);
            }

            foreach ($request->steps as $index => $stepContent) {
                $project->steps()->create([
                    'content' => [['insert' => "$stepContent\n"]],
                    'order' => $index + 1
                ]);
            }
            DB::commit();
            $project->load('materials');
            return ResponseHelper::jsonWithData(201, 'Saved successfully', new ProjectResource($project));
        } catch (\Exception $e) {
            DB::rollBack();

            return ResponseHelper::json('500', $e->getMessage());
        }
    }
    public function fork(Request $request, Project $project)
    {
        $user = auth()->user();

        if (!$user) {
            return ResponseHelper::json(401, 'Unauthorized');
        }

        $forkedProject = $project->replicate();
        $forkedProject->user_id = $user->id;
        $forkedProject->parent_id = $project->id;
        $forkedProject->save();

        $forkedProject->tags()->sync($project->tags->pluck('id'));

        foreach ($project->steps as $step) {
            $clonedStep = $step->replicate();
            $forkedProject->steps()->save($clonedStep);
        }

        foreach ($project->materials as $material) {
            $forkedProject->materials()->save($material);
        }

        if ($request->has('start')) {
            foreach ($project->usedMaterials as $material) {

                $forkedProject->usedMaterials()->save($material, ['material_quantity' => $material->pivot->material_quantity]);
            }
            // Get materials that belong to current user
            $userMaterials = $project->usedMaterials()
                ->where('user_id', $user->id)
                ->pluck('id');

            // Detach only materials owned by user
            $project->usedMaterials()->detach($userMaterials);
        }


        return ResponseHelper::jsonWithData(200, 'Project forked successfully', ['projectId' => $forkedProject->id, 'title' => $forkedProject->title]);
    }

    private function incrementView(Project $project)
    {
        $user = auth('sanctum')->user();

        if (!$user) return;

        if ($project->user_id !== $user->id) {
            $project->views()->firstOrCreate(['user_id' => $user->id]);
        }
    }

    public function createProject(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'tags' => 'nullable|string',
            'visibility' => 'required|integer',
            'category' => 'required|integer',
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
            'project_category_id' => $request->input('category'),
        ]);

        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
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
        $this->incrementView($project);
        $project->load(['materials', 'steps', 'projectCategory']);
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

        $project->description = json_decode($request->input('description'), true);
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
            'category' => 'required|integer',
            'forkable' => 'required|boolean',
        ]);

        $user = auth()->user();
        $projectUserId = $project->user_id;

        if ($validate->fails() || $user->id != $projectUserId) {
            return ResponseHelper::errInput();
        }

        $project->title = $request->input('title');
        $project->project_category_id = $request->input('category');
        $project->forkable = $request->input('forkable');
        $project->save();

        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tags = collect($tagNames)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName]);
            });

            $project->tags()->sync($tags->pluck('id'));
        } else {
            $project->tags()->sync([]);
        }

        $project->load(['materials', 'steps', 'projectCategory']);
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
