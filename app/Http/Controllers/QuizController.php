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

        // Ambil soal untuk schedule ini secara random
        $questions = Question::where('quiz_schedule_id', $schedule->id)
            ->inRandomOrder()
            ->get();

        return view('quiz.start', compact('schedule', 'quizSession', 'questions'));
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

        // Jika warning_count masih kurang dari 2, artinya user submit manual
        if ($quizSession->warning_count < 2) {
            $quizSession->status = 'submitted';
        } else {
            // Jika sudah pelanggaran (warning_count >= 2), tetap force_submitted
            $quizSession->status = 'force_submitted';
        }

        $quizSession->end_time = now();
        $quizSession->save();

        return response()->json([
            'message' => 'Exam submitted',
            'status' => $quizSession->status
        ]);
    }

    public function submitAnswer(Request $request, QuizSchedule $schedule)
    {
        // Validasi input minimal
        $data = $request->validate([
            'quiz_session_id' => 'required|integer',
            'question_id'     => 'required|integer',
            'answer'          => 'required|string',
        ]);

        // Update atau buat record jawaban
        $quizAnswer = QuizAnswer::updateOrCreate(
            [
                'quiz_session_id' => $data['quiz_session_id'],
                'question_id'     => $data['question_id'],
            ],
            [
                'answer' => $data['answer'],
                // Jika perlu, Anda dapat menghitung is_correct di sini
                'is_correct' => false,
            ]
        );

        return response()->json(['message' => 'Jawaban disimpan']);
    }
}
