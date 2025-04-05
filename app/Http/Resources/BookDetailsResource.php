<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Book (id)' => $this->id,
            'Book (title)' => $this->title,
            'Book (image)' => $this->image,
            'Book (details)' =>new Book_DetailsResource($this->whenloaded('details')),
        ];
    }
}
