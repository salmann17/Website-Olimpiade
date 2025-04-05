<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPeserta;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware([\Illuminate\Auth\Middleware\Authenticate::class])->group(function () {

    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/admin/dashboard', function () {return view('admin.dashboard');})->name('admin.dashboard');
        Route::get('/admin/peserta/create', [AdminController::class, 'createPeserta'])->name('admin.peserta.create');
        Route::post('/admin/peserta/store', [AdminController::class, 'storePeserta'])->name('admin.peserta.store');
        Route::get('/admin/peserta/list', [AdminController::class, 'listPeserta'])->name('admin.peserta.list');
        Route::post('/admin/peserta/update', [AdminController::class, 'updatePeserta'])->name('admin.peserta.update');
        Route::get('/admin/pantau', [AdminController::class, 'pantauUjian'])->name('admin.pantau.ujian');
    });


    Route::middleware([IsPeserta::class])->group(function () {
        Route::get('/peserta/dashboard', [\App\Http\Controllers\UsersController::class, 'dashboardPeserta'])->name('peserta.dashboard');
        Route::get('/peserta/riwayat', [\App\Http\Controllers\UsersController::class, 'riwayatUjian'])->name('peserta.riwayat');
        Route::get('/quiz/{schedule}', [QuizController::class, 'start'])->name('quiz.start');
        Route::post('/quiz/{schedule}/answer', [QuizController::class, 'submitAnswer'])->name('quiz.answer');
        Route::post('/quiz/{schedule}/warning', [QuizController::class, 'warning'])->name('quiz.warning');
        Route::post('/quiz/{schedule}/finish', [QuizController::class, 'finish'])->name('quiz.finish');

        
    });
});
