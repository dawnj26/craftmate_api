<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function reply(Request $request, Project $project, Comment $comment)
    {
        $validate = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validate->fails()) {
            return ResponseHelper::errInput();
        }

        $user = auth()->user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'parent_id' => $comment->id,
            'content' => $request->input('comment'),
        ]);

        return ResponseHelper::jsonWithData(201, 'Reply success', new CommentResource($comment));
    }

    public function getComments(Project $project)
    {
        $comments = $project->comments()
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return ResponseHelper::jsonWithData(200, 'Comments retrieved.', [
            'comments' => CommentResource::collection($comments),
        ]);
    }

    public function toggleLike(Project $project, Comment $comment)
    {
        $user = auth()->user();

        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            $comment->likes()->create([
                'user_id' => $user->id,
            ]);
        }

        return ResponseHelper::json(200, 'Toggled');
    }

    public function createComment(Request $request, Project $project)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validate->fails()) {
            return ResponseHelper::json(422, 'Missing required fields or validation error');
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'content' => $request->input('comment'),
        ]);

        $comment->load('user');

        return ResponseHelper::jsonWithData(201, 'Created successfully!', new CommentResource($comment));
    }

    public function update(Request $request, Project $project, Comment $comment)
    {
        $user = auth()->user();

        if ($user->id !== $comment->user_id) {
            return ResponseHelper::json(400, 'Unauthorized');
        }

        $validate = Validator::make($request->all(), [
            'comment' => 'required',
        ]);

        if ($validate->fails()) {
            return ResponseHelper::errInput();
        }

        $comment->content = $request->input('comment');
        $comment->save();

        return ResponseHelper::updated();
    }

    public function delete(Comment $comment)
    {
        $user = auth()->user();

        if ($user->id !== $comment->user_id) {
            return ResponseHelper::json(400, 'Unauthorized');
        }

        $comment->delete();

        return ResponseHelper::json(200, 'Deleted successfully');
    }
}
