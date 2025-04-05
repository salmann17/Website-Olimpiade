@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75] py-8 px-4">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Hasil Ujian</h2>

        {{-- Dropdown untuk memilih babak (schedule) --}}
        <form action="{{ route('admin.hasil.ujian') }}" method="GET" class="mb-6 flex items-center gap-2">
            <label for="schedule_id" class="text-sm font-medium text-gray-700">Pilih Babak:</label>
            <select name="schedule_id" id="schedule_id"
                class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                <option value="">-- Pilih Babak --</option>
                @foreach($schedules as $sch)
                <option value="{{ $sch->id }}" {{ $selectedScheduleId == $sch->id ? 'selected' : '' }}>
                    {{ $sch->title }}
                </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Tampilkan
            </button>
        </form>

        @if($selectedScheduleId)
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing: 0;">
                <thead class="bg-gradient-to-br from-[#48dbfb] to-[#5f27cd]">
                    <tr>
                        <th class="px-4 py-2 text-left text-white font-medium">No</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Username</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Durasi (menit)</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Waktu</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $index => $session)
                    @php
                    $rowNumber = ($sessions->currentPage() - 1) * $sessions->perPage() + $index + 1;

                    // menghitung kolom waktu
                    $timeRange = '';
                    if ($session->start_time && $session->end_time) {
                    $timeRange = $session->start_time->format('H:i:s') . ' - ' . $session->end_time->format('H:i:s');
                    } elseif ($session->start_time) {
                    $timeRange = $session->start_time->format('H:i:s') . ' - (Belum selesai)';
                    } else {
                    $timeRange = '(Belum mulai)';
                    }

                    // Menghitung kolom durasi
                    $durationStr = '0 detik';
                    if ($session->start_time && $session->end_time) {
                    $seconds = abs($session->end_time->diffInSeconds($session->start_time, false));
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $secondsLeft = $seconds % 60;
                    $durationParts = [];
                    if ($hours > 0) {
                    $durationParts[] = $hours . ' jam';
                    }
                    if ($minutes > 0) {
                    $durationParts[] = $minutes . ' menit';
                    }
                    $durationParts[] = $secondsLeft . ' detik';
                    $durationStr = implode(' ', $durationParts);
                    }

                    // Menghitung skor
                    $score = $session->correct_count * 10;
                    @endphp
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-200 transition-colors">
                        <td class="px-4 py-2 border-b">{{ $rowNumber }}</td>
                        <td class="px-4 py-2 border-b">{{ $session->user->username }}</td>
                        <td class="px-4 py-2 border-b">{{ $durationStr }}</td>
                        <td class="px-4 py-2 border-b">{{ $timeRange }}</td>
                        <td class="px-4 py-2 border-b font-semibold">{{ $score }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-6">
            {{ $sessions->links('vendor.pagination.tailwind') }}
        </div>
        @else
        <p class="text-gray-700">Silakan pilih babak terlebih dahulu.</p>
        @endif
    </div>
</div>
@endsection