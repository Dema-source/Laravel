<?php

namespace Database\Seeders;

use App\Models\Auther;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $authers = [
        //     'Hana Mina',
        //     'Hadi Haidar',
        //     'Hazar Ghazal',
        //     'Ahlam Mastaghanmi'
        // ];
        // foreach ($authers as $auther) {
        //     Auther::create(['name' => $auther]);
        // }
        Auther::Factory()->count(5)->create();
    }
}
