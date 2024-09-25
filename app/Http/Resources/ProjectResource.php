<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('sanctum')->user();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'parent_id' => $this->parent_id,
            'is_public' => $this->is_public,
            'user' => $this->user,
            'tags' => $this->tags,
            'is_liked' => $user ? $this->isLikedByUser($user) : false,
            'like_count' => $this->likes()->count(),
            'comment_count' => $this->comments()->whereNull('parent_id')->count(),
            'fork_count' => $this->child()->count(),
            'steps' => $this->step,
        ];
    }
}
