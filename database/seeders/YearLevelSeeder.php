<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = array("1st", "2nd", "3rd", "4th", "5th");

        for ($i=0; $i<count($levels); $i++) {
            DB::table('year_levels')->insert([
                'id' => $i+1,
                'name' => $levels[$i],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
