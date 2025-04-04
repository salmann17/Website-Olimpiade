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

        foreach ($schedules as $schedule) {
            $now = now();
            if (isset($userSessions[$schedule->id])) {
                $schedule->status = $userSessions[$schedule->id]->status;
            } else {
                if ($now->lt($schedule->start_time)) {
                    $schedule->status = 'not_open'; 
                } elseif ($now->between($schedule->start_time, $schedule->end_time)) {
                    $schedule->status = 'available'; 
                } else {
                    $schedule->status = 'expired'; 
                }
            }
        }

        return view('peserta.dashboard', compact('schedules', 'userSessions'));
    }
}
