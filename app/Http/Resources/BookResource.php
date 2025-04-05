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
            'Book (file)' => $this->pdf_link ? Storage::url($this->pdf_link) : ($this->doc_link ? Storage::url($this->doc_link) : null),
        ];
    }
}
