<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('categories')->insert([
        ['name' => 'Fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Non-fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Fantasy', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Science Fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Romance', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Thriller', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Mystery', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Horror', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Biography', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Autobiography', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Historical Fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Self-help', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Poetry', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Drama', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Young Adult', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Children\'s Books', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Comics', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Graphic Novels', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Cookbooks', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Health & Wellness', 'created_at' => now(), 'updated_at' => now()],
    ]);
    }
}
