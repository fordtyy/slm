<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{

  //hashed password = abc12345

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $admin = array(
      "name" => "Admin",
      "email" => "admin@email.com",
      "email_verified_at" => Carbon::now(),
      "usn" => "20240985672",
      "password" => '$2y$12$dWGkvZ5oSAlbQvfslmIvFu2v6SUG0LhRillM4RqV/W8iTyz1sh.fu',
      "type" => "admin",
      "year_level_id" => "2",
      "course_id" => "1",
      "created_at" => Carbon::now(),
      "updated_at" => Carbon::now()
    );

    $student = array(
      "name" => "Student",
      "email" => "student@email.com",
      "email_verified_at" => Carbon::now(),
      "usn" => "20242234365",
      "password" => '$2y$12$dWGkvZ5oSAlbQvfslmIvFu2v6SUG0LhRillM4RqV/W8iTyz1sh.fu',
      "type" => "student",
      "year_level_id" => "4",
      "course_id" => "2",
      "created_at" => Carbon::now(),
      "updated_at" => Carbon::now()
    );

    $student1 = array(
      "name" => "Student One",
      "email" => "student1@email.com",
      "email_verified_at" => Carbon::now(),
      "usn" => "20242234366",
      "password" => '$2y$12$dWGkvZ5oSAlbQvfslmIvFu2v6SUG0LhRillM4RqV/W8iTyz1sh.fu',
      "type" => "student",
      "year_level_id" => "4",
      "course_id" => "2",
      "created_at" => Carbon::now(),
      "updated_at" => Carbon::now()
    );

    $student2 = array(
      "name" => "Student Two",
      "email" => "student2@email.com",
      "email_verified_at" => Carbon::now(),
      "usn" => "20242234367",
      "password" => '$2y$12$dWGkvZ5oSAlbQvfslmIvFu2v6SUG0LhRillM4RqV/W8iTyz1sh.fu',
      "type" => "student",
      "year_level_id" => "4",
      "course_id" => "2",
      "created_at" => Carbon::now(),
      "updated_at" => Carbon::now()
    );

    $users = [$admin, $student, $student1, $student2];

    foreach ($users as $x) {
      DB::table('users')->insert([
        'name' => $x['name'],
        'email' => $x['email'],
        'usn' => $x['usn'],
        'password' => $x['password'],
        'type' => $x['type'],
        'course_id' => $x['course_id'],
        'year_level_id' => $x['year_level_id'],
        'email_verified_at' => $x['email_verified_at'],
        'created_at' => now(),
        'updated_at' => now()
      ]);
    }
  }
}
