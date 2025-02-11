<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => $this->collection,
            'nextPageUrl' => $this->nextPageUrl(),
            'currentPage' => $this->currentPage(),
            'pageSize' => $this->perPage(),
            'totalItems' => $this->total(),
            'totalPages' => $this->lastPage(),
        ];
    }
}
