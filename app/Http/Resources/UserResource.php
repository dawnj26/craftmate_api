<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('sanctum')->user();
        $profile = $this->profile;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image' => $profile->image,
            'bio' => $profile->bio,
            'followerCount' => $this->followers()->count(),
            'followingCount' => $this->following()->count(),
            'projectCount' => $this->projects()->count(),
            'isFollowed' => $user === null ? false : $this->isFollowedByUser($user),
        ];
    }
}
