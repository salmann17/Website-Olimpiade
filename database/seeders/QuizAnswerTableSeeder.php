<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizAnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = QuizSession::all();

        foreach ($sessions as $session) {
            $questions = Question::where('quiz_schedule_id', $session->quiz_schedule_id)->get();

            foreach ($questions as $question) {
                QuizAnswer::create([
                    'quiz_session_id' => $session->id,
                    'question_id' => $question->id,
                    'answer' => $question->correct_answer,
                    'is_correct' => true
                ]);
            }
        }
    }
}
