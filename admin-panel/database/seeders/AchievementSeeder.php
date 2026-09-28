<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single achievement record with default values
        Achievement::create([
            'training_programs' => 50,
            'registered_students' => 1000,
            'academy_books' => 25,
            'ready_instructors' => 15,
        ]);
    }
}
