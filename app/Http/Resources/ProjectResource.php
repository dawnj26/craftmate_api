<?php

namespace App\Http\Resources;

use App\Models\Step;
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
            'description' => json_decode($this->description),
            'imageUrl' => $this->image_path,
            'parentId' => $this->parent_id,
            'visibility' => $this->visibility()->first()->name,
            'creator' => $this->user,
            'tags' => $this->tags,
            'isLiked' => $user ? $this->isLikedByUser($user) : false,
            'likeCount' => $this->likes()->count(),
            'commentCount' => $this->comments()->whereNull('parent_id')->count(),
            'forkCount' => $this->children()->count(),
            'steps' => StepResource::collection($this->steps),
            'viewCount' => $this->views()->count(),
            'updatedAt' => $this->updated_at,
            'createdAt' => $this->created_at,
            'deletedAt' => $this->deleted_at,
            'forkedFrom' => $this->when($this->parent, function () {
                $user = auth('sanctum')->user();
                return [
                    'id' => $this->id,
                    'title' => $this->title,
                    'description' => json_decode($this->description),
                    'imageUrl' => $this->image_path,
                    'parentId' => $this->parent_id,
                    'visibility' => $this->visibility()->first()->name,
                    'creator' => $this->user,
                    'tags' => $this->tags,
                    'isLiked' => $user ? $this->isLikedByUser($user) : false,
                    'likeCount' => $this->likes()->count(),
                    'commentCount' => $this->comments()->whereNull('parent_id')->count(),
                    'forkCount' => $this->children()->count(),
                    'steps' => StepResource::collection($this->steps),
                    'viewCount' => $this->views()->count(),
                    'updatedAt' => $this->updated_at,
                    'createdAt' => $this->created_at,
                    'deletedAt' => $this->deleted_at,
                ];
            }),
        ];
    }
}
