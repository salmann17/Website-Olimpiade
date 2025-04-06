<?php

namespace App\Http\Controllers;

use App\Models\QuizSchedule;
use App\Models\QuizSession;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function listPeserta()
    {
        $peserta = User::where('role', 'peserta')->paginate(15);

        return view('admin.list-peserta', compact('peserta'));
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

        // Subquery untuk menandai user yang punya session di schedule terpilih
        $subSql = 'exists (
            select 1 from quiz_sessions 
            where quiz_sessions.user_id = users.id 
            and quiz_sessions.quiz_schedule_id = ?
        )';

        // Query user peserta dengan search, urutkan yang sudah ikut ujian dulu
        $userQuery = User::where('role', 'peserta')
            ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"))
            ->orderByRaw("$subSql desc", [$selectedScheduleId])
            ->orderBy('username');

        // Ambil sessions dengan count jawaban benar, keyed by user_id
        $sessions = collect();
        if ($selectedScheduleId) {
            $sessions = QuizSession::withCount([
                'answers as correct_count' => fn($q) => $q->where('is_correct', true)
            ])
                ->where('quiz_schedule_id', $selectedScheduleId)
                ->get()
                ->keyBy('user_id');
        }

        // Paginate 15
        $users = $userQuery->paginate(15)->appends([
            'schedule_id' => $selectedScheduleId,
            'search'      => $search,
        ]);

        return view('admin.hasil-ujian', compact(
            'schedules',
            'selectedScheduleId',
            'search',
            'users',
            'sessions'
        ));
    }
}
