<?php

namespace Database\Seeders;

use App\Models\Book_Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Book_CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book_Category::Factory()->count(2)->create();
    }
}
