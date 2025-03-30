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
                'id' => 1,
                'title' => 'Babak Penyisihan 1',
                'start_time' => Carbon::now()->subMinutes(10),
                'end_time' => Carbon::now()->addHours(2),
                'duration' => 60,
                'total_questions' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Babak Penyisihan 2',
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDay()->addHours(2),
                'duration' => 90,
                'total_questions' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'Babak Semifinal',
                'start_time' => Carbon::now()->addDays(2),
                'end_time' => Carbon::now()->addDays(2)->addHours(2),
                'duration' => 120,
                'total_questions' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
