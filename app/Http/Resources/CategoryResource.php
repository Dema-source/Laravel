<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return   [
            'Category id = ' => $this->id,
            'Category name = ' => $this->name,
            // 'Category created date = ' => $this->created_at->format('Y,M,D')
        ];
    }
}
