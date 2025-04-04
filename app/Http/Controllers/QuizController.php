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

        // Simpan atau perbarui jawaban dengan pengecekan kebenaran
        if ($request->has('answers') && is_array($request->input('answers'))) {
            foreach ($request->input('answers') as $data) {
                if (isset($data['question_id']) && isset($data['answer'])) {
                    // Ambil soal untuk mengecek jawaban yang benar
                    $question = Question::find($data['question_id']);
                    $isCorrect = 0;
                    if ($question) {
                        // Samakan case keduanya sebelum dibandingkan
                        $correctNormalized = strtolower($question->correct_answer);
                        $answerNormalized  = strtolower($data['answer']);
                    
                        if ($correctNormalized === $answerNormalized) {
                            $isCorrect = 1;
                        }
                    }
                    
                    // Gunakan updateOrCreate agar jawaban tersimpan (atau diperbarui)
                    QuizAnswer::updateOrCreate(
                        [
                            'quiz_session_id' => $quizSession->id,  // Pastikan nama kolom sesuai dengan migrasi dan model
                            'question_id' => $data['question_id'],
                        ],
                        [
                            'answer' => $data['answer'],
                            'is_correct' => $isCorrect,
                        ]
                    );
                }
            }
        }

        // Update status quiz_session: jika warning_count < 2, berarti submit manual → 'submitted'
        if ($quizSession->warning_count < 2) {
            $quizSession->status = 'submitted';
        } else {
            $quizSession->status = 'force_submitted';
        }
        $quizSession->end_time = now();
        $quizSession->save();

        return response()->json([
            'message' => 'Exam submitted',
            'status' => $quizSession->status
        ]);
        return redirect()->route('peserta.dashboard')->with('success', 'Jawaban berhasil dikirim!');
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
