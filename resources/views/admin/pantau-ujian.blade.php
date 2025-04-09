@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-[#f6f9fc] via-[#b6cbe0] to-[#002855] py-8 px-4">
    <div class="max-w-5xl mx-auto bg-transparent rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-[#004a80]">Pemantauan Ujian</h2>

        <form id="filter-form" action="{{ route('admin.pantau.ujian') }}" method="GET" class="mb-4 flex gap-2">
            <select name="schedule_id" class="px-3 py-2 border rounded" onchange="document.getElementById('filter-form').submit()">
                <option value="">-- Pilih Babak --</option>
                @foreach($schedules as $sch)
                <option value="{{ $sch->id }}" {{ $selectedScheduleId==$sch->id?'selected':'' }}>
                    {{ $sch->title }}
                </option>
                @endforeach
            </select>
            <input type="text" name="search" placeholder="Cari username..." value="{{ $search }}"
                class="px-3 py-2 border rounded flex-1" onkeydown="if(event.key==='Enter') document.getElementById('filter-form').submit()">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Cari</button>
        </form>

        @if($selectedScheduleId)
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing:0">
                <thead class="bg-[#004a80]">
                    <tr>
                        <th class="px-4 py-2 text-white">No</th>
                        <th class="px-4 py-2 text-white">Username</th>
                        <th class="px-4 py-2 text-white">Deteksi</th>
                        <th class="px-4 py-2 text-white">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                    @php
                    $session = $sessions->get($user->id);
                    $rowNumber = ($users->currentPage()-1)*$users->perPage()+$i+1;
                    @endphp
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-200 group">
                        <td class="px-4 py-2 border text-center">{{ $rowNumber }}</td>
                        <td class="px-4 py-2 border text-center">{{ $user->username }}</td>
                        @if($session)
                        @php
                        if ($session->warning_count == 0) {
                        $txt = 'Aman';
                        $bg = 'bg-green-300';
                        $groupHover = 'group-hover:bg-green-500';
                        } elseif ($session->warning_count == 1) {
                        $txt = 'Peringatan';
                        $bg = 'bg-yellow-300';
                        $groupHover = 'group-hover:bg-yellow-500';
                        } else {
                        $txt = 'Diskualifikasi';
                        $bg = 'bg-red-300';
                        $groupHover = 'group-hover:bg-red-500';
                        }

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
                        }
                        } else {
                        $statusText = '–';
                        }
                        @endphp
                        <td class="px-4 py-2 border {{ $bg }} {{ $groupHover }} text-center">{{ $txt }}</td>
                        <td class="px-4 py-2 border text-center">{{ $statusText }}</td>
                        @else
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