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
            "usn" => "20242234364",
            "password" => '$2y$12$dWGkvZ5oSAlbQvfslmIvFu2v6SUG0LhRillM4RqV/W8iTyz1sh.fu',
            "type" => "student",
            "year_level_id" => "4",
            "course_id" => "2",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        );

        DB::table('users')->insert([
            'name' => $admin['name'],
            'email' => $admin['email'],
            'usn' => $admin['usn'],
            'password' => $admin['password'],
            'type' => $admin['type'],
            'course_id' => $admin['course_id'],
            'year_level_id' => $admin['year_level_id'],
            'email_verified_at' => $admin['email_verified_at'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insert([
            'name' => $student['name'],
            'email' => $student['email'],
            'usn' => $student['usn'],
            'course_id' => $student['course_id'],
            'year_level_id' => $student['year_level_id'],
            'password' => $student['password'],
            'type' => $student['type'],
            'email_verified_at' => $student['email_verified_at'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
