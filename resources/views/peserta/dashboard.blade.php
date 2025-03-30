@extends('layout.app')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="grid md:grid-cols-3 gap-6 px-4">
        @php
            $babakLabels = [
                1 => 'Babak Penyisihan 1',
                2 => 'Babak Penyisihan 2',
                3 => 'Babak Semifinal'
            ];
        @endphp

        @foreach ($babakLabels as $babak => $judul)
            @php
                $session = $sessions->get($babak);
                $soal = $jumlahSoal[$babak] ?? 0;
            @endphp
            <div class="rounded-xl overflow-hidden shadow-lg bg-white text-gray-800 w-full">
                <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
                    <h3 class="font-bold text-lg">{{ strtoupper($judul) }}</h3>
                </div>

                <div class="px-4 py-2 text-sm bg-blue-100 text-blue-900">
                    <p>{{ $soal }} Soal / {{ $session?->duration ?? '-' }} menit</p>
                </div>

                <div class="px-4 py-2 bg-gray-100 text-sm flex justify-between items-center">
                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                        {{ $session?->start_time ?? '-' }}
                    </span>
                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                        {{ $session?->end_time ?? '-' }}
                    </span>
                </div>

                <div class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2 cursor-pointer text-sm">
                    <a href="#" class="flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i> MULAI
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
