<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Book_Category;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Book_CategoryFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id'=>Book::inrandomOrder()->first()->id,
            'category_id'=>Category::inrandomOrder()->first()->id,
        ];
    }
}
