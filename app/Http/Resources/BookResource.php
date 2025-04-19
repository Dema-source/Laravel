<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
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
            'Book (file_path)' => $this->file_path ? Storage::url($this->file_path) : null,
            'Book (file_type)' => $this->file_type ? $this->file_type : null,
        ];
    }
}
