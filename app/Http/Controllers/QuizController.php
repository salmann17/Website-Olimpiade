<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizSchedule;
use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function start(Request $request, QuizSchedule $schedule)
    {
        $user = Auth::user();

        $quizSession = QuizSession::create([
            'user_id'           => $user->id,
            'quiz_schedule_id'  => $schedule->id,
            'start_time'        => now(),
            'status'            => 'in_progress',
            'warning_count'     => 0,
        ]);

        $questions = Question::where('quiz_schedule_id', $schedule->id)
            ->inRandomOrder()
            ->get();

        return view('quiz.start', compact('schedule', 'quizSession', 'questions'));
    }

    public function warning(Request $request, QuizSchedule $schedule)
    {
        $quizSession = QuizSession::findOrFail($request->quiz_session_id);
        $quizSession->warning_count = 1;
        $quizSession->save();

        return response()->json([
            'message' => 'Warning issued',
            'warning_count' => $quizSession->warning_count
        ]);
    }
    public function finish(Request $request, QuizSchedule $schedule)
    {
        $quizSession = QuizSession::findOrFail($request->quiz_session_id);
    
        // Jika request menyertakan array 'answers', artinya ini manual submit
        if ($request->has('answers') && is_array($request->input('answers'))) {
            foreach ($request->input('answers') as $data) {
                if (isset($data['question_id']) && isset($data['answer'])) {
                    $question = Question::find($data['question_id']);
                    $isCorrect = 0;
                    if ($question) {
                        // Normalisasi jawaban untuk perbandingan case-insensitive
                        $correctNormalized = strtolower($question->correct_answer);
                        $answerNormalized  = strtolower($data['answer']);
                        if ($correctNormalized === $answerNormalized) {
                            $isCorrect = 1;
                        }
                    }
                    QuizAnswer::updateOrCreate(
                        [
                            'quiz_session_id' => $quizSession->id,
                            'question_id'     => $data['question_id'],
                        ],
                        [
                            'answer'     => $data['answer'],
                            'is_correct' => $isCorrect,
                        ]
                    );
                }
            }
            // Jika manual submit dan warning_count kurang dari 2, status jadi submitted
            if ($quizSession->warning_count < 2) {
                $quizSession->status = 'submitted';
            } else {
                // Jika ada pelanggaran sebelumnya, status tetap force_submitted
                $quizSession->status = 'force_submitted';
            }
        } else {
            // Jika tidak ada 'answers' di request, artinya ini force finish
            // Update warning_count ke 2 dan status force_submitted
            $quizSession->warning_count = 2;
            $quizSession->status = 'force_submitted';
        }
    
        $quizSession->end_time = now();
        $quizSession->save();
    
        return response()->json([
            'message' => 'Exam submitted',
            'status'  => $quizSession->status
        ]);
    }
    

    public function submitAnswer(Request $request, QuizSchedule $schedule)
    {
        $data = $request->validate([
            'quiz_session_id' => 'required|integer',
            'question_id'     => 'required|integer',
            'answer'          => 'required|string',
        ]);

        $quizAnswer = QuizAnswer::updateOrCreate(
            [
                'quiz_session_id' => $data['quiz_session_id'],
                'question_id'     => $data['question_id'],
            ],
            [
                'answer' => $data['answer'],
                'is_correct' => false,
            ]
        );

        return response()->json(['message' => 'Jawaban disimpan']);
    }
}
