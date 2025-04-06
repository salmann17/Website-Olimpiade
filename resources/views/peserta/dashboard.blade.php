@extends('layout.app')

@section('content')
<div class="flex items-center justify-center min-h-screen px-4">
    <div class="grid md:grid-cols-3 gap-6 w-full max-w-6xl">
        @foreach ($schedules as $schedule)
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

            {{-- Tampilkan status berdasarkan flag yang telah ditetapkan --}}
            @if ($schedule->status === 'not_open')
            <div class="bg-red-100 text-red-800 text-center py-2 font-semibold">
                Belum dibuka
            </div>
            @elseif ($schedule->status === 'available')
            <div class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2">
                <a href="javascript:void(0)" onclick="openExamWindow({{ $schedule->id }})" class="inline-flex items-center gap-2 justify-center">
                    <i class="fas fa-play"></i> Mulai
                </a>
            </div>
            @elseif ($schedule->status === 'submitted' || $schedule->status === 'force_submitted')
            <div class="bg-green-100 text-green-800 text-center py-2 font-semibold">
                Sudah dikerjakan
            </div>
            @elseif ($schedule->status === 'expired')
            <div class="bg-yellow-100 text-yellow-800 text-center py-2 font-semibold">
                Waktu habis
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
<input type="hidden" id="user-id" value="{{ Auth::id() }}">

<script>
    function openExamWindow(scheduleId) {
        const url = "{{ route('quiz.show', ':id') }}".replace(':id', scheduleId);

        const features = "toolbar=no,location=no,directories=no,status=no,menubar=no," +
            "scrollbars=yes,resizable=yes," +
            "width=" + screen.width + ",height=" + screen.height + "," +
            "left=0,top=0";

        const examWindow = window.open(url, "_blank", features);

        if (examWindow) {
            examWindow.focus();
        } else {
            alert("Popup terblokir! Mohon izinkan popup untuk situs ini.");
        }
    }
</script>
@endsection