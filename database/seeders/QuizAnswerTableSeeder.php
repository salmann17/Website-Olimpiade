<?php

namespace Database\Seeders;

use App\Models\QuizAnswer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizAnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuizAnswer::create([
            'session_id' => 1,
            'question_id' => 1,
            'answer' => 'B',
            'is_correct' => true,
        ]);
    }
}
