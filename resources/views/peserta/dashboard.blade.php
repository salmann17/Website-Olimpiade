@extends('layout.app')

@section('content')
<div class="flex items-center justify-center min-h-screen px-4">
    <div class="grid md:grid-cols-3 gap-6 w-full max-w-6xl">
        @foreach ($schedules as $schedule)
            @php
                $session = $userSessions[$schedule->id] ?? null;
                $isNow = now()->between($schedule->start_time, $schedule->end_time);
            @endphp

            <div class="rounded-xl overflow-hidden shadow-lg bg-white text-gray-800">
                <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
                    <h3 class="font-bold text-lg">{{ strtoupper($schedule->title) }}</h3>
                    <span class="bg-green-500 text-xs px-2 py-1 rounded">OSN</span>
                </div>

                <div class="px-4 py-2 text-sm bg-blue-100 text-blue-900">
                    <p>{{ $schedule->questions->count() }} Soal / {{ $schedule->duration }} menit</p>
                </div>

                <div class="px-4 py-2 bg-gray-100 text-sm">
                    <p><strong>Waktu:</strong></p>
                    <p class="text-xs text-gray-700">{{ $schedule->start_time }} s/d {{ $schedule->end_time }}</p>
                </div>

                @if ($session)
                    <div class="px-4 py-2 text-sm bg-green-100 text-green-800">
                        <p>Status: <strong>{{ strtoupper($session->status) }}</strong></p>
                    </div>
                @endif

                <div class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2">
                    @if ($isNow && !$session)
                        <a href="#" class="flex items-center justify-center gap-2">
                            <i class="fas fa-play"></i> Mulai
                        </a>
                    @elseif (!$isNow)
                        <span class="text-white text-sm">Belum dibuka</span>
                    @elseif ($session)
                        <span class="text-white text-sm">Sudah dikerjakan</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
