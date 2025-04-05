<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Book_DetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'Book (Isbn) = ' => $this->isbn,
                'Book (number_of_pages) = ' => $this->number_of_pages,
                'Book (publication_date) = ' => $this->publication_date
            ];
    }
}
