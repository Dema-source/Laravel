<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book_Details extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
