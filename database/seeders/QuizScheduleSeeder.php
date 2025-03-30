<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quiz_schedules')->insert([
            [
                'quiz_round' => 1,
                'start_time' => Carbon::now()->addMinutes(1),
                'end_time' => Carbon::now()->addHours(2),
                'duration' => 60,
                'total_questions' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_round' => 2,
                'start_time' => Carbon::now()->addDays(1),
                'end_time' => Carbon::now()->addDays(1)->addHours(2),
                'duration' => 90,
                'total_questions' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_round' => 3,
                'start_time' => Carbon::now()->addDays(2),
                'end_time' => Carbon::now()->addDays(2)->addHours(2),
                'duration' => 120,
                'total_questions' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
