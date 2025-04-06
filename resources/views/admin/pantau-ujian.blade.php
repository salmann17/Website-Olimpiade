@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75] py-8 px-4">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Pemantauan Ujian</h2>

        <form action="{{ route('admin.pantau.ujian') }}" method="GET" class="mb-6 flex items-center gap-2">
            <label>Pilih Babak:</label>
            <select name="schedule_id" class="px-3 py-2 border rounded">
                <option value="">-- Pilih Babak --</option>
                @foreach($schedules as $sch)
                <option value="{{ $sch->id }}" {{ $selectedScheduleId == $sch->id ? 'selected' : '' }}>
                    {{ $sch->title }}
                </option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Tampilkan</button>
        </form>

        @if($selectedScheduleId)
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing: 0;">
                <thead class="bg-gradient-to-br from-[#48dbfb] to-[#5f27cd]">
                    <tr>
                        <th class="px-4 py-2 text-white">No</th>
                        <th class="px-4 py-2 text-white">Username</th>
                        <th class="px-4 py-2 text-white">Deteksi</th>
                        <th class="px-4 py-2 text-white">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    @php
                    $session = $sessions->get($user->id);
                    $rowNumber = $index + 1;
                    @endphp
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-200">
                        <td class="px-4 py-2 border text-center">{{ $rowNumber }}</td>
                        <td class="px-4 py-2 border text-center">{{ $user->username }}</td>
                        @if($session)
                        @php
                        if ($session->warning_count == 0) {
                        $deteksiText = 'Aman'; $deteksiBg = 'bg-green-200';
                        } elseif ($session->warning_count == 1) {
                        $deteksiText = 'Peringatan'; $deteksiBg = 'bg-yellow-200';
                        } else {
                        $deteksiText = 'Diskualifikasi'; $deteksiBg = 'bg-red-200';
                        }
                        @endphp
                        <td class="px-4 py-2 border {{ $deteksiBg }} text-center">{{ $deteksiText }}</td>
                        <td class="px-4 py-2 border text-center">{{ $session->status }}</td>
                        @else
                        <td class="px-4 py-2 border text-center">–</td>
                        <td class="px-4 py-2 border text-center">–</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection