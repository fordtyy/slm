<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorBook extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('author_book')->insert([
          ['book_id' => '1', 'author_id' => '12', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '2', 'author_id' => '2', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '3', 'author_id' => '17', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '4', 'author_id' => '15', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '5', 'author_id' => '9', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '6', 'author_id' => '20', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '7', 'author_id' => '8', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '8', 'author_id' => '16', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '9', 'author_id' => '5', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '10', 'author_id' => '6', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '11', 'author_id' => '14', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '12', 'author_id' => '13', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '13', 'author_id' => '10', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '14', 'author_id' => '11', 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '15', 'author_id' => '19', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
