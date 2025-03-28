<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // nanti kita buat view-nya
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $credentials['username'])
            ->where('password', md5($credentials['password']))
            ->first();

        if ($user) {
            session(['user' => $user]); // simpan user ke session
            return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'peserta.dashboard');
        }

        return back()->withErrors(['login_gagal' => 'Username atau password salah.']);
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('login');
    }
}
