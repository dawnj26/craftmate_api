<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    public function getComments(Project $project)
    {
        $comments = $project->comments()->get();
        $comments->load('user', 'children');

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Comments retrieved successfully'
            ],
            'data' => [
                'comments' => CommentResource::collection($comments),
            ],
        ], 200);
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

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Toggled',
            ]
        ], 200);
    }
}
