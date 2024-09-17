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

        return [
            'id' => $this->id,
            'content' => $this->content,
            'like_count' => $this->likes()->count(),
            'children' => CommentResource::collection($this->whenLoaded('children')),
            'user' => new UserResource($this->whenLoaded('user')),
            'is_liked' => $user === null ? false : $this->isLikedByUser($user),
        ];
    }
}
