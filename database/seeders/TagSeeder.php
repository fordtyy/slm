<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('tags')->insert([
        ['name' => 'Fantasy', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Science Fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Romance', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Mystery', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Thriller', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Horror', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Historical Fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Biography', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Self-help', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Non-fiction', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Adventure', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Dystopian', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Contemporary', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Classics', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Young Adult', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Children\'s', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Poetry', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Drama', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Crime', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Humor', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Autobiography', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Philosophy', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Spirituality', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Health', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Cooking', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Travel', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Science', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Art', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Technology', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Memoir', 'created_at' => now(), 'updated_at' => now()],
    ]); 
    }
}
