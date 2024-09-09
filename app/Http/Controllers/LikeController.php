<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Project $project)
    {
        $user = $request->user();

        $existingLike = $project->likes()->where('user_id', $user->id)->first();

        if ($existingLike)
        {
            $existingLike->delete();
        }
        else
        {
            $project->likes()->create([
                'user_id' => $user->id,
                'project_id' => $project->id,
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
