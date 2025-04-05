<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::Factory()->count(10)->create();
        // $books = [
        //     [
        //         'auther_id' => 1,
        //         'title' => 'Sunday',
        //         'image' => 'storage/app/public/my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg'
        //     ],
        //     [
        //         'auther_id' => 2,
        //         'title' => 'Monday',
        //         'image' => 'storage/app/public/my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg'
        //     ],
        //     [
        //         'auther_id' => 3,
        //         'title' => 'Tuseday',
        //         'image' => 'storage/app/public/my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg'
        //     ],
        //     [
        //         'auther_id' => 1,
        //         'title' => 'Wednesday',
        //         'image' => 'storage/app/public/my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg'
        //     ],
        //     [
        //         'auther_id' => 4,
        //         'title' => 'Friday',
        //         'image' => 'storage/app/public/my book photo/0PV4RmTnjweVf7luXbJQT36dO2N6flTwVADwuE1Y.jpg'
        //     ]
        // ];
        // foreach ($books as $book)
        //     Book::create($book);
    }
}
