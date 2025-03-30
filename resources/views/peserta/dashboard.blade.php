@extends('layout.app')

@section('content')
<div class="flex items-center justify-center min-h-screen px-4">
    <div class="grid md:grid-cols-3 gap-6 w-full max-w-6xl">
        @foreach ($schedules as $schedule)
        @php
        $session = $userSessions[$schedule->id] ?? null;
        $isNow = now()->between($schedule->start_time, $schedule->end_time);
        @endphp
        @php
        $now = now();
        @endphp

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4 flex justify-between">
                <h3 class="font-bold">{{ strtoupper($schedule->title) }}</h3>
                <span class="text-xs bg-green-400 text-white px-2 py-1 rounded">GenBI</span>
            </div>

            <div class="px-4 py-2 text-sm bg-blue-100 text-blue-900">
                <p>{{ $schedule->questions->count() }} Soal / {{ $schedule->duration }} menit</p>
            </div>

            <div class="px-4 py-2 bg-gray-100 text-sm">
                <p><strong>Waktu:</strong></p>
                <p class="text-xs text-gray-700">
                    {{ $schedule->start_time }} s/d {{ $schedule->end_time }}
                </p>
            </div>

            {{-- STATUS BERDASARKAN WAKTU --}}
            @if ($now->lt($schedule->start_time))
            <div class="bg-red-100 text-red-800 text-center py-2 font-semibold">
                Belum dibuka
            </div>
            @elseif ($now->between($schedule->start_time, $schedule->end_time))
            <div class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2">
                <a href="#" class="inline-flex items-center gap-2 justify-center">
                    <i class="fas fa-play"></i> Mulai
                </a>
            </div>
            @else
            <div class="bg-green-100 text-green-800 text-center py-2 font-semibold">
                Sudah dikerjakan
            </div>
            @endif
        </div>

        @endforeach
    </div>
</div>
@endsection