<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('book_tag')->insert([
          ['book_id' => '1', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '1', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '2', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '2', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '3', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '3', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '4', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '4', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '5', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '5', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '6', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '6', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '7', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '7', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '8', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '8', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '9', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '9', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '10', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '10', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '11', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '11', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '12', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '12', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '13', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '13', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '14', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '14', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '15', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()],
          ['book_id' => '15', 'tag_id' => Tag::inRandomOrder()->first()->id, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
