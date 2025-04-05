<?php

namespace Database\Factories;

use App\Models\Book;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book_Details>
 */
class Book_DetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => fake()->isbn13(),
            'number_of_pages' => fake()->numberBetween(50, 500),
            'publication_date' => fake()->date()
        ];
    }
}
