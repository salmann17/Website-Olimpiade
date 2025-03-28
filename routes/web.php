<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', function () {return 'Halaman Admin';})->name('admin.dashboard');
});

Route::middleware('peserta')->group(function () {
    Route::get('/peserta/dashboard', function () {return 'Halaman Peserta';})->name('peserta.dashboard');
});
