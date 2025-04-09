@extends('layout.app')

@section('content')
<div class="container mx-auto py-6 p-6">
    <h1 class="text-2xl font-bold mb-4 text-[#004a80]">Riwayat Ujian</h1>

    @forelse($sessions as $session)
    @php
    // menghitung total soal dari schedule
    $totalQuestions = $session->schedule->questions->count();

    // menghitung jumlah soal yang dijawab (answer tidak kosong)
    $answeredCount = $session->answers->filter(function($ans) {
    return $ans->answer !== '';
    })->count();

    // Sisanya tidak terjawab
    $notAnswered = $totalQuestions - $answeredCount;

    // Durasi yang digunakan (jika end_time terisi)

    $sec = abs($session->start_time->diffInSeconds($session->end_time));
    $h = floor($sec / 3600);
    $m = floor(($sec % 3600) / 60);
    $s = $sec % 60;
    $parts = [];
    if ($h) $parts[] = "$h jam";
    if ($m) $parts[] = "$m menit";
    $parts[] = "$s detik";
    $durationUsed = implode(' ', $parts);

    $statusText = '';
    if ($session) {
    switch ($session->status) {
    case 'in_progress':
    $statusText = 'Sedang Mengerjakan';
    break;
    case 'submitted':
    $statusText = 'Telah Dikerjakan';
    break;
    case 'force_submitted':
    $statusText = 'Terkena Pelanggaran';
    break;
    default:
    $statusText = $session->status;
    break;
    }}
    @endphp

    <div class="bg-[#004a80] rounded shadow p-4 mb-4">
        <h2 class="text-xl font-bold mb-2 text-white">
            {{ $session->schedule->title }}
        </h2>
        <p class="text-sm text-white mb-1">
            <strong>Waktu Ujian:</strong>
            {{ $session->start_time }} - {{ $session->end_time }}
        </p>
        <p class="text-sm text-white mb-1">
            <strong>Status:</strong>
            {{ $statusText }}
        </p>
        <p class="text-sm text-white mb-1">
            <strong>Durasi yang Digunakan:</strong>
            {{ $durationUsed }}
        </p>
        <p class="text-sm text-white mb-1">
            <strong>Total Soal:</strong>
            {{ $totalQuestions }}
        </p>
        <p class="text-sm text-white mb-1">
            <strong>Terjawab:</strong>
            {{ $answeredCount }}
        </p>
        <p class="text-sm text-white mb-1">
            <strong>Tidak Terjawab:</strong>
            {{ $notAnswered }}
        </p>
    </div>
    @empty
    <div class="bg-white rounded shadow p-4 mb-4">
        <p class="text-white-800">Belum ada riwayat ujian.</p>
    </div>
    @endforelse
</div>
@endsection