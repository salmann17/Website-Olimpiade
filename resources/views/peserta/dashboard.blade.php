@extends('layout.app')

@section('content')
<style>
    .watermark-container::before {
        content: '';
        position: fixed;
        top: 25%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-image: url("{{ asset('bi.png') }}");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0.15;
        width: 75%;
        height: 75%;
        z-index: 0;
        pointer-events: none;
    }
</style>
<div class="flex items-center justify-center min-h-screen px-4 watermark-container">
    <div class="grid md:grid-cols-3 gap-6 w-full max-w-6xl relative z-10">
        @foreach ($schedules as $schedule)
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-[#004a80] text-white p-4 flex justify-between">
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
            <div class="bg-[#004a80] hover:bg-blue-300 text-white text-center py-2">
                <a href="javascript:void(0)" onclick="checkSession({{ $schedule->id }})" class="inline-flex items-center gap-2 justify-center">
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
            @else
            <div class="bg-gray-100 text-gray-800 text-center py-2 font-semibold">
                Anda sedang mengerjakan
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
<input type="hidden" id="user-id" value="{{ Auth::id() }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function checkSession(scheduleId) {
        fetch("{{ route('quiz.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    schedule_id: scheduleId,
                    user_id: "{{ Auth::id() }}"
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        title: 'Info',
                        text: 'Anda sudah mengerjakan ujian ini.',
                        icon: 'info',
                        confirmButtonText: 'Oke',
                        confirmButtonColor: '#3085d6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    openExamWindow(scheduleId);
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memeriksa sesi ujian.', 'error');
            });
    }

    function openExamWindow(scheduleId) {
        const url = "{{ url('/quiz') }}/" + scheduleId;
        const features = "toolbar=no,location=no,status=no,menubar=no," +
            "scrollbars=yes,resizable=yes," +
            "width=" + screen.width + ",height=" + screen.height;
        const win = window.open(url, "_blank", features);
        if (win) win.focus();
        else alert("Popup terblokir!");
    }
</script>

@endsection