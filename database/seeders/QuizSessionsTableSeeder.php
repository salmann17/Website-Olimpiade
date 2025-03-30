<?php

namespace Database\Seeders;

use App\Models\QuizSession;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSessionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'peserta')->first();

        foreach ([1, 2, 3] as $scheduleId) {
            QuizSession::create([
                'user_id' => $user->id,
                'quiz_schedule_id' => $scheduleId,
                'start_time' => now(),
                'end_time' => now()->addMinutes(60),
                'skor' => 0,
                'warning_count' => 0,
                'status' => 'not_started',
            ]);
        }
    }
}
