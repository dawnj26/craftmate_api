<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function deleteUserProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_ids' => 'required|array',

        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $projectIds = $request->input('project_ids');

        try {
            DB::beginTransaction();

            $deletedCount = Project::whereIn('id', $projectIds)->delete();

            DB::commit();

            return response()->json([
                'message' => "{$deletedCount} project(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::json(500, 'An error occurred while deleting projects');
        }

        return ResponseHelper::json(200, 'Projects deleted');
    }

    public function getLatest()
    {
        $projects = Project::where('visibility_id', 1)->latest()->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved',  new ProjectCollection($projects));
    }

    public function getUserProjects(Request $request, User $user)
    {
        if ($request->has('q')) {
            $projects = $user->projects()->where('title', 'like', "%{$request->input('q')}%")->latest()->paginate(10);
            return ResponseHelper::jsonWithData(200, 'Projects retrieved',  new ProjectCollection($projects));
        }

        $projects = $user->projects()->latest()->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved',  new ProjectCollection($projects));
    }

    public function getCurrentUserProjects(Request $request, int $visibility = null)
    {
        $user = auth()->user();
        $query = $user->projects();

        if ($request->has('q')) {
            $query->where('title', 'like', "%{$request->input('q')}%");
        }

        if ($visibility) {
            $query->where('visibility_id', $visibility);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $allowedSorts = ['updated_at', 'created_at', 'like_count', 'comment_count', 'title'];

            if (!in_array($sortBy, $allowedSorts)) {
                return ResponseHelper::errInput();
            }

            $order = $request->input('order', 'desc');
            if (!in_array($order, ['asc', 'desc'])) {
                return ResponseHelper::errInput();
            }

            if ($sortBy === 'like_count') {
                $query->withCount('likes')
                ->orderBy('likes_count', $order);
            } else if ($sortBy === 'comment_count') {
                $query->withCount('comments')
                ->orderBy('comments_count', $order);
            } else {

                $query->orderBy($sortBy, $order);
            }
        } else {
            $query->latest('updated_at');
        }

        $projects = $query->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved', new ProjectCollection($projects));
    }
}
