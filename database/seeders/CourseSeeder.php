<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = array(
            "Computer Science" => "CS",
            "Information Technology" => "IT",
            "Hotel Management" => "HM",
            "Business Administration" => "BA",
            "Accounting Technology" => "AT",
            "Information System" => "IS"
        );

        foreach($courses as $key => $value) { 
            DB::table('courses')->insert([
                'name' => $key,
                'code' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
