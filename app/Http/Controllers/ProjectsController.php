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
use App\Models\Material;

class ProjectsController extends Controller
{
    public function getTrending(Request $request)
    {
        $query = Project::query();

        if ($request->has('timeframe')) {
            $timeframe = $request->input('timeframe');
            switch ($timeframe) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'all_time':
                default:
                    // No additional constraints
                    break;
            }
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $allowedSorts = ['views_count', 'likes_count', 'comments_count'];

            if (!in_array($sortBy, $allowedSorts)) {
                return ResponseHelper::errInput();
            }

            $order = $request->input('order', 'desc');
            if (!in_array($order, ['asc', 'desc'])) {
                return ResponseHelper::errInput();
            }

            if ($sortBy === 'views_count') {
                $query->withCount('views')
                    ->orderBy('views_count', $order);
            } else if ($sortBy === 'likes_count') {
                $query->withCount('likes')
                    ->orderBy('likes_count', $order);
            } else if ($sortBy === 'comments_count') {
                $query->withCount('comments')
                    ->orderBy('comments_count', $order);
            }
        } else {
            $query->latest('created_at');
        }

        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('project_category_id', $categoryId);
        }

        $projects = $query->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Trending projects retrieved', new ProjectCollection($projects));
    }

    public function getFollowing(Request $request)
    {
        $user = auth()->user();
        $followingIds = $user->following()->pluck('id');
        $categoryId = $request->input('category_id');

        $query = Project::whereIn('user_id', $followingIds)
            ->where('visibility_id', 1);

        if ($categoryId) {
            $query->where('project_category_id', $categoryId);
        }

        $projects = $query->latest()->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved', new ProjectCollection($projects));
    }

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

    public function getLatest(Request $request)
    {
        $query = Project::where('visibility_id', 1)->latest();

        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('project_category_id', $categoryId);
        }

        $projects = $query->paginate(10);

        return ResponseHelper::jsonWithData(200, 'Projects retrieved', new ProjectCollection($projects));
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

        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('project_category_id', $categoryId);
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
