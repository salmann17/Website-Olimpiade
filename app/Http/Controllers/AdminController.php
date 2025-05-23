<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizSchedule;
use App\Models\QuizSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function createPeserta()
    {
        return view('admin.create-peserta');
    }

    public function storePeserta(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'fullname' => 'required|string|max:255',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'fullname' => $request->fullname,
            'role'     => 'peserta',
        ]);

        return redirect()->route('admin.peserta.create')->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function listPeserta(Request $request)
    {
        $search = $request->get('search');

        $pesertaQuery = User::where('role', 'peserta')
            ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"));

        $peserta = $pesertaQuery->paginate(15)
            ->appends(['search' => $search]);

        return view('admin.list-peserta', compact('peserta', 'search'));
    }


    public function updatePeserta(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer|exists:users,id',
            'username' => 'required|string',
            'fullname' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($request->id);

        $user->username = $request->username;
        $user->fullname = $request->fullname;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diperbarui.',
        ]);
    }

    public function pantauUjian(Request $request)
    {
        $schedules = QuizSchedule::all();
        $selectedScheduleId = $request->get('schedule_id', 0);
        $search = $request->get('search');

        $subSql = 'exists (select 1 from quiz_sessions where quiz_sessions.user_id = users.id and quiz_sessions.quiz_schedule_id = ?)';

        $userQuery = User::where('role', 'peserta')
            ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"))
            ->orderByRaw("$subSql desc", [$selectedScheduleId])
            ->orderBy('username');

        $users = $userQuery->paginate(15)->appends([
            'schedule_id' => $selectedScheduleId,
            'search'      => $search,
        ]);

        $sessions = collect();
        if ($selectedScheduleId) {
            $sessions = QuizSession::where('quiz_schedule_id', $selectedScheduleId)
                ->get()
                ->keyBy('user_id');
        }

        return view('admin.pantau-ujian', compact(
            'schedules',
            'selectedScheduleId',
            'users',
            'sessions',
            'search'
        ));
    }

    public function hasilUjian(Request $request)
    {
        $schedules = QuizSchedule::all();
        $selectedScheduleId = $request->get('schedule_id', 0);
        $search = $request->get('search');

        $userQuery = User::where('role', 'peserta')
            ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"));

        if ($selectedScheduleId) {
            $userQuery->leftJoin('quiz_sessions', function ($join) use ($selectedScheduleId) {
                $join->on('users.id', '=', 'quiz_sessions.user_id')
                    ->where('quiz_sessions.quiz_schedule_id', $selectedScheduleId);
            })
                ->select(
                    'users.*',
                    DB::raw("TIMESTAMPDIFF(SECOND, quiz_sessions.start_time, quiz_sessions.end_time) as duration"),
                    'quiz_sessions.skor'
                )
                ->orderBy('quiz_sessions.skor', 'desc')
                ->orderByRaw("IFNULL(TIMESTAMPDIFF(SECOND, quiz_sessions.start_time, quiz_sessions.end_time), 9999999) ASC");
        } else {
            $userQuery->orderBy('username');
        }

        $users = $userQuery->paginate(15)->appends([
            'schedule_id' => $selectedScheduleId,
            'search'      => $search,
        ]);

        $sessions = collect();
        if ($selectedScheduleId) {
            $sessions = QuizSession::where('quiz_schedule_id', $selectedScheduleId)
                ->get()
                ->keyBy('user_id');
        }

        return view('admin.hasil-ujian', compact(
            'schedules',
            'selectedScheduleId',
            'search',
            'users',
            'sessions'
        ));
    }


    public function dashboard()
    {
        $stages = ['Babak Penyisihan 1', 'Babak Penyisihan 2', 'Babak Semifinal'];
        $warningData = [];

        foreach ($stages as $stage) {
            $schedule = QuizSchedule::where('title', $stage)->first();

            $warningCounts = QuizSession::where('quiz_schedule_id', $schedule->id)
                ->selectRaw('warning_count, COUNT(*) as total')
                ->groupBy('warning_count')
                ->pluck('total', 'warning_count')
                ->toArray();

            $warningData[] = [
                $warningCounts[0] ?? 0,
                $warningCounts[1] ?? 0,
                $warningCounts[2] ?? 0
            ];
        }

        $scoreData = [];

        foreach ($stages as $stage) {
            $schedule = QuizSchedule::where('title', $stage)->first();

            $scores = QuizSession::where('quiz_schedule_id', $schedule->id)
                ->selectRaw('FLOOR(skor/10)*10 as range_start, COUNT(*) as count')
                ->groupBy('range_start')
                ->orderBy('range_start')
                ->get()
                ->pluck('count', 'range_start')
                ->toArray();

            $scoreDistribution = array_fill(0, 10, 0);
            foreach ($scores as $range => $count) {
                $index = $range / 10;
                $scoreDistribution[$index] = $count;
            }

            $scoreData[] = $scoreDistribution;
        }

        return view('admin.dashboard', compact('warningData', 'scoreData'));
    }

    public function export(Request $request)
    {
        $selectedScheduleId = $request->get('schedule_id', 0);
        $search = $request->get('search');

        $userQuery = User::where('role', 'peserta')
            ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"));

        if ($selectedScheduleId) {
            $userQuery->leftJoin('quiz_sessions', function ($join) use ($selectedScheduleId) {
                $join->on('users.id', '=', 'quiz_sessions.user_id')
                    ->where('quiz_sessions.quiz_schedule_id', $selectedScheduleId);
            })
                ->select(
                    'users.*',
                    DB::raw("TIMESTAMPDIFF(SECOND, quiz_sessions.start_time, quiz_sessions.end_time) as duration"),
                    'quiz_sessions.skor'
                )
                ->orderBy('quiz_sessions.skor', 'desc')
                ->orderByRaw("IFNULL(TIMESTAMPDIFF(SECOND, quiz_sessions.start_time, quiz_sessions.end_time), 9999999) ASC");
        } else {
            $userQuery->orderBy('username');
        }

        $users = $userQuery->get();

        $data = [];
        $rowNumber = 1;
        foreach ($users as $user) {
            $session = QuizSession::where('quiz_schedule_id', $selectedScheduleId)
                ->where('user_id', $user->id)
                ->first();

            if ($session && $session->start_time && $session->end_time) {
                $sec = abs($session->end_time->diffInSeconds($session->start_time));
                $h = floor($sec / 3600);
                $m = floor(($sec % 3600) / 60);
                $s = $sec % 60;
                $parts = [];
                if ($h) $parts[] = "$h jam";
                if ($m) $parts[] = "$m menit";
                $parts[] = "$s detik";
                $dur = implode(' ', $parts);
                $time = $session->start_time->format('H:i:s') . ' - ' . $session->end_time->format('H:i:s');
                $skor = $session->skor;
            } else {
                $dur = '–';
                $time = '–';
                $skor = '–';
            }

            $data[] = [
                'No' => $rowNumber++,
                'Username' => $user->fullname,
                'Durasi' => $dur,
                'Waktu' => $time,
                'Skor' => $skor,
            ];
        }

        return response()->json($data);
    }

    public function exportDetail($userId)
    {
        $session = QuizSession::where('user_id', $userId)->where('quiz_schedule_id', 3)->first();
        if (!$session) return response()->json([]);

        $answers = QuizAnswer::where('quiz_session_id', $session->id)->get();
        $questions = Question::whereIn('id', $answers->pluck('question_id'))->get()->keyBy('id');

        $result = $answers->map(function ($ans) use ($questions) {
            return [
                'question' => $questions[$ans->question_id]->question ?? 'N/A',
                'answer'   => $ans->answer,
                'comment'  => $ans->comment
            ];
        });


        return response()->json($result);
    }
}
