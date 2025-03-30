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
        $session = QuizSession::firstOrCreate([
            'user_id' => $user->id,
            'quiz_schedule_id' => $schedule->id
        ], [
            'start_time' => now(),
            'status' => 'in_progress'
        ]);

        $questions = Question::where('quiz_schedule_id', $schedule->id)->get();
        $answers = QuizAnswer::where('quiz_session_id', $session->id)->pluck('answer', 'question_id');

        return view('quiz.start', compact('schedule', 'questions', 'answers', 'session'));
    }

    public function submitAnswer(Request $request, QuizSchedule $schedule)
    {
        $session = QuizSession::where('user_id', Auth::id())
                    ->where('quiz_schedule_id', $schedule->id)->firstOrFail();

        QuizAnswer::updateOrCreate(
            [
                'quiz_session_id' => $session->id,
                'question_id' => $request->question_id
            ],
            ['answer' => $request->answer]
        );

        return response()->json(['status' => 'saved']);
    }

    public function warning(Request $request, QuizSchedule $schedule)
    {
        $session = QuizSession::where('user_id', Auth::id())
                    ->where('quiz_schedule_id', $schedule->id)->firstOrFail();

        $session->increment('warning_count');

        if ($session->warning_count >= 2) {
            $session->update([
                'status' => 'force_submitted',
                'end_time' => now(),
            ]);
            return response()->json(['force_submit' => true]);
        }

        return response()->json(['warning_count' => $session->warning_count]);
    }

    public function finish(Request $request, QuizSchedule $schedule)
    {
        $session = QuizSession::where('user_id', Auth::id())
                    ->where('quiz_schedule_id', $schedule->id)->firstOrFail();

        $session->update([
            'status' => 'submitted',
            'end_time' => now(),
        ]);

        return redirect()->route('peserta.dashboard')->with('success', 'Jawaban berhasil dikirim!');
    }
}

