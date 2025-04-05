<?php

namespace App\Http\Resources;

use App\Models\Book;
use App\Models\User;
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
        return [
            'book :' => Book::find($this->book_id)->title,
            'user :' => User::find($this->user_id)->name,
            'comment :' => $this->comment_text,
            'date :' => $this->created_at
        ];
    }
}
