@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75] py-8 px-4">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Hasil Ujian</h2>

        <form id="filter-form" action="{{ route('admin.hasil.ujian') }}" method="GET" class="mb-4 flex gap-2">
            <select name="schedule_id" class="px-3 py-2 border rounded"
                onchange="document.getElementById('filter-form').submit()">
                <option value="">-- Pilih Babak --</option>
                @foreach($schedules as $sch)
                <option value="{{ $sch->id }}" {{ $selectedScheduleId==$sch->id?'selected':'' }}>
                    {{ $sch->title }}
                </option>
                @endforeach
            </select>
            <input type="text" name="search" placeholder="Cari username..." value="{{ $search }}"
                class="px-3 py-2 border rounded flex-1"
                onkeydown="if(event.key==='Enter') document.getElementById('filter-form').submit()">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Cari</button>
        </form>

        @if($selectedScheduleId)
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing:0">
                <thead class="bg-gradient-to-br from-[#48dbfb] to-[#5f27cd]">
                    <tr>
                        <th class="px-4 py-2 text-white">No</th>
                        <th class="px-4 py-2 text-white">Username</th>
                        <th class="px-4 py-2 text-white">Durasi</th>
                        <th class="px-4 py-2 text-white">Waktu</th>
                        <th class="px-4 py-2 text-white">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                    @php
                    $session = $sessions->get($user->id);
                    $rowNumber = ($users->currentPage()-1)*$users->perPage()+$i+1;
                    @endphp
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-200">
                        <td class="px-4 py-2 border text-center">{{ $rowNumber }}</td>
                        <td class="px-4 py-2 border text-center">{{ $user->username }}</td>
                        @if($session && $session->start_time && $session->end_time)
                        @php
                        // Durasi
                        $sec = abs($session->end_time->diffInSeconds($session->start_time));
                        $h = floor($sec / 3600);
                        $m = floor(($sec % 3600) / 60);
                        $s = $sec % 60;
                        $parts = [];
                        if ($h) $parts[] = "$h jam";
                        if ($m) $parts[] = "$m menit";
                        $parts[] = "$s detik";
                        $dur = implode(' ', $parts);
                        // Waktu (format jam)
                        $time = $session->start_time->format('H:i:s') . ' - ' . $session->end_time->format('H:i:s');
                        // Skor
                        $score = ($session->correct_count ?? 0) * 10;
                        @endphp
                        <td class="px-4 py-2 border text-center">{{ $dur }}</td>
                        <td class="px-4 py-2 border text-center">{{ $time }}</td>
                        <td class="px-4 py-2 border text-center">{{ $score }}</td>
                        @else
                        <td class="px-4 py-2 border text-center">–</td>
                        <td class="px-4 py-2 border text-center">–</td>
                        <td class="px-4 py-2 border text-center">–</td>
                        @endif
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection