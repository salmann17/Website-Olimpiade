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
    // 1. Mulai Ujian: Insert record quiz session dan render view ujian
    public function start(Request $request, QuizSchedule $schedule)
    {
        $user = Auth::user();

        // Buat record baru di quiz_sessions
        $quizSession = QuizSession::create([
            'user_id'           => $user->id,
            'quiz_schedule_id'  => $schedule->id,
            'start_time'        => now(),
            'status'            => 'in_progress',
            'warning_count'     => 0,
        ]);

        // Render view ujian dan kirim data schedule dan quizSession
        return view('quiz.start', compact('schedule', 'quizSession'));
    }

    // 3. Endpoint untuk meng-update warning_count pertama kali
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

    // 4. Endpoint untuk meng-update warning_count kedua (force finish)
    public function finish(Request $request, QuizSchedule $schedule)
    {
        $quizSession = QuizSession::findOrFail($request->quiz_session_id);
        $quizSession->warning_count = 2;
        $quizSession->status = 'force_submitted';
        $quizSession->end_time = now();
        $quizSession->save();

        return response()->json([
            'message' => 'Exam force submitted',
            'status' => $quizSession->status
        ]);
    }
}
