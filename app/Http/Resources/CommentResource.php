<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('sanctum')->user();
        $this->load('user', 'children');

        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'content' => $this->content,
            'likeCount' => $this->likes()->count(),
            'children' => CommentResource::collection($this->whenLoaded('children')),
            'user' => new UserResource($this->whenLoaded('user')),
            'isLiked' => $user === null ? false : $this->isLikedByUser($user),
        ];
    }
}
