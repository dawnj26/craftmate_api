<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Models\Step;
use Illuminate\Contracts\Auth\Authenticatable;
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
            'imageUrl' => $this->image_path,
            'parentId' => $this->parent_id,
            'visibility' => $this->visibility()->first()->name,
            'creator' => $this->user,
            'tags' => $this->tags,
            'isLiked' => $user ? $this->isLikedByUser($user) : false,
            'likeCount' => $this->likes()->count(),
            'commentCount' => $this->comments()->whereNull('parent_id')->count(),
            'forkCount' => $this->children()->count(),
            'steps' => $this->whenLoaded('steps', function() {
                return StepResource::collection($this->steps);
            }),
            'viewCount' => $this->views()->count(),
            'updatedAt' => $this->updated_at,
            'createdAt' => $this->created_at,
            'deletedAt' => $this->deleted_at,
            'fork' => $this->when($this->parent, function() {
                $parent = $this->parent;

                return [
                    'id' => $parent->id,
                    'title' => $parent->title,
                ];
            }),
            'materials' => $this->whenLoaded('materials', function() {
                return MaterialResource::collection($this->materials);
            }),
            'category' => $this->whenLoaded('projectCategory', function() {
                return new ProjectCategoryResource($this->projectCategory);
            }),
        ];
    }
}
