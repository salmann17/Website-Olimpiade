<?php

namespace Database\Seeders;

use App\Models\QuizSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSessionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuizSession::create([
            'user_id' => 2,
            'babak' => 1,
            'start_time' => now()->subMinutes(10),
            'end_time' => now(),
            'duration' => 600,
            'skor' => 10,
            'warning_count' => 0,
            'status' => 'submitted',
        ]);
    }
}
