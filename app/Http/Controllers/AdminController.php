<?php

namespace App\Http\Controllers;

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
}
