<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutherBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Auther id = '=>$this->id,
            'Auther name = '=>$this->name,
            'Auther created date = '=>$this->created_at->format('Y,M,D'),
            'Auther books' => BookResource::collection($this->whenLoaded('books'))
        ];
    }
}
