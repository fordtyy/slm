<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('authors')->insert([
        ['name' => 'Jane Austen', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Charles Dickens', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Mark Twain', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Virginia Woolf', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'George Orwell', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Harper Lee', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Leo Tolstoy', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Fyodor Dostoevsky', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Ernest Hemingway', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'J.K. Rowling', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'J.R.R. Tolkien', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Agatha Christie', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'William Shakespeare', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Herman Melville', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Emily Brontë', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Gabriel García Márquez', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Chinua Achebe', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Toni Morrison', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'James Joyce', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Franz Kafka', 'created_at' => now(), 'updated_at' => now()],
    ]);
    }
}
