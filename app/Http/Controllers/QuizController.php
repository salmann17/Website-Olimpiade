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
    public function show(QuizSchedule $schedule)
    {
        $user = Auth::user();

        $quizSession = QuizSession::firstOrCreate([
            'user_id' => $user->id,
            'quiz_schedule_id' => $schedule->id
        ], [
            'start_time' => now(),
            'status' => 'in_progress',
            'warning_count' => 0,
        ]);

        $questions = Question::where('quiz_schedule_id', $schedule->id)
            ->inRandomOrder()
            ->get();

        return view('quiz.start', compact('schedule', 'quizSession', 'questions'));
    }

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

        if ($request->has('answers') && is_array($request->input('answers'))) {
            foreach ($request->input('answers') as $data) {
                if (isset($data['question_id']) && isset($data['answer'])) {
                    $question = Question::find($data['question_id']);
                    $isCorrect = 0;
                    if ($question) {
                        $correctNormalized = strtolower($question->correct_answer);
                        $answerNormalized  = strtolower($data['answer']);
                        if ($correctNormalized === $answerNormalized) {
                            $isCorrect = 1;
                        }
                        elseif ($question->type === 'true_false') {
                            $normalizedChoices = ['true', 'false'];
                            if (in_array($correctNormalized, $normalizedChoices) && in_array($answerNormalized, $normalizedChoices)) {
                                if ($correctNormalized === $answerNormalized) {
                                    $isCorrect = 1;
                                }
                            }
                        }
                    }

                    $comment = isset($data['comment']) ? $data['comment'] : null;

                    QuizAnswer::updateOrCreate(
                        [
                            'quiz_session_id' => $quizSession->id,
                            'question_id'     => $data['question_id'],
                        ],
                        [
                            'answer'     => $data['answer'],
                            'is_correct' => $isCorrect,
                            'comment'    => $comment,
                        ]
                    );
                }
            }
        }

        if ($request->boolean('force')) {
            $quizSession->warning_count = 2;
            $quizSession->status = 'force_submitted';
        } else {
            if ($quizSession->warning_count < 2) {
                $quizSession->status = 'submitted';
            } else {
                $quizSession->status = 'force_submitted';
            }
        }

        $quizSession->end_time = now();

        if (in_array($schedule->id, [1, 2])) {
            $correctCount = QuizAnswer::where('quiz_session_id', $quizSession->id)
                ->where('is_correct', true)
                ->count();
            $quizSession->skor = $correctCount * 2;
        } elseif (in_array($schedule->id, [3])) {
            $correctCount = QuizAnswer::where('quiz_session_id', $quizSession->id)
                ->where('is_correct', true)
                ->count();
            $quizSession->skor = $correctCount * (100 / 30);
        } 
        
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

    public function checkSession(Request $request)
    {
        $exists = QuizSession::where('user_id', $request->user_id)
            ->where('quiz_schedule_id', $request->schedule_id)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

}
