<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Book_Details;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Book_DetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $booksIds = Book::pluck('id')->toArray();
        if (empty($booksIds)) {
            throw new \Exception('There is no available books to add details for it');
        }
        foreach ($booksIds as $bookId) {
            Book_Details::factory()->create([
                'book_id' => $bookId,
            ]);
        }
    }
}
