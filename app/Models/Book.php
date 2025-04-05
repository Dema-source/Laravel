<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function auther()
    {
        return $this->belongsTo(Auther::class);
    }

    public function details()
    {
        return $this->hasOne(Book_Details::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function users_ratings()
    {
        return $this->belongsToMany(User::class,'ratings');
    }
}
