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

        $selectedScheduleId = $request->get('schedule_id');

        $sessions = collect(); 

        if ($selectedScheduleId) {
            $sessions = QuizSession::with('user')
                ->where('quiz_schedule_id', $selectedScheduleId)
                ->paginate(15);
        }

        return view('admin.pantau-ujian', compact('schedules', 'sessions', 'selectedScheduleId'));
    }

    public function hasilUjian(Request $request)
    {
        $schedules = QuizSchedule::all();
        $selectedScheduleId = $request->get('schedule_id');

        $sessions = collect();

        if ($selectedScheduleId) {
            $sessions = QuizSession::with('user')
                ->withCount([
                    'answers as correct_count' => function ($query) {
                        $query->where('is_correct', true);
                    }
                ])
                ->where('quiz_schedule_id', $selectedScheduleId)
                ->paginate(15);
        }

        return view('admin.hasil-ujian', compact('schedules', 'sessions', 'selectedScheduleId'));
    }
}
