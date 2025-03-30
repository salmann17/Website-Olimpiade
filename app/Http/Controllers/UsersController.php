<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizSchedule;
use App\Models\QuizSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function dashboardPeserta()
    {
        $user = Auth::user();

        $schedules = QuizSchedule::with('questions')->get();

        $userSessions = QuizSession::where('user_id', $user->id)->get()->keyBy('quiz_schedule_id');

        return view('peserta.dashboard', compact('schedules', 'userSessions'));
    }
}
