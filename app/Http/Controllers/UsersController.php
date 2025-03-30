<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function dashboardPeserta()
    {
        $userId = Auth::id();
        $sessions = QuizSession::where('user_id', $userId)->get()->keyBy('babak');

        // Hitung jumlah soal per babak
        $jumlahSoal = Question::selectRaw('babak, COUNT(*) as total')
            ->groupBy('babak')
            ->pluck('total', 'babak'); // hasil: [1 => 20, 2 => 30, ...]

        return view('peserta.dashboard', compact('sessions', 'jumlahSoal'));
    }
}
