<?php

namespace Database\Factories;

use App\Models\Auther;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auther_id' => Auther::inrandomOrder()->first()->id,
            'title' => fake()->sentence(),
            // 'image' => 'my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg',
            'image' => fake()->imageUrl(640, 480, 'books', true) ?: 'https://via.placeholder.com/640x480.png?text=No+Image',
        ];
    }
}
