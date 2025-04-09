@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-[#f6f9fc] via-[#b6cbe0] to-[#002855] py-8 px-4">
    <div class="max-w-5xl mx-auto bg-transparent rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-white">Hasil Ujian</h2>

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
        <div class="mb-4">
            <button id="export-btn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                Export Excel
            </button>
        </div>
        @endif

        @if($selectedScheduleId)
        <div class="overflow-x-auto">
            <table id="hasil-ujian-table" class="min-w-full border-separate" style="border-spacing:0">
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
                        @endphp
                        <td class="px-4 py-2 border text-center">{{ $dur }}</td>
                        <td class="px-4 py-2 border text-center">{{ $time }}</td>
                        <td class="px-4 py-2 border text-center">{{ $session->skor }}</td>
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
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script>
    const scheduleTitle = "{{ optional($schedules->firstWhere('id', $selectedScheduleId))->title ?? '' }}";
    let fileName = 'hasilujian';
    if (scheduleTitle) {
        fileName += '_' + scheduleTitle.toLowerCase().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '');
    }

    document.getElementById('export-btn').addEventListener('click', function() {
        fetch("{{ route('admin.hasil.ujian.export') }}?schedule_id={{ $selectedScheduleId }}&search={{ $search }}")
            .then(res => res.json())
            .then(data => {
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Hasil Ujian');

                const headerRow = worksheet.addRow(["No", "Username", "Durasi", "Waktu", "Skor"]);
                headerRow.eachCell((cell) => {
                    cell.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: 'FF5F27CD'
                        } 
                    };
                    cell.font = {
                        bold: true,
                        color: {
                            argb: 'FFFFFFFF'
                        }
                    };
                    cell.alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };
                });

                data.forEach((row, index) => {
                    const excelRow = worksheet.addRow([
                        row.No,
                        row.Username,
                        row.Durasi,
                        row.Waktu,
                        row.Skor
                    ]);
                    const fillColor = (index % 2 === 0) ? 'FFD1E8FF' : 'FFE5E5E5';
                    excelRow.eachCell((cell) => {
                        cell.fill = {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: fillColor
                            }
                        };
                    });
                });

                worksheet.columns.forEach(column => {
                    let maxLength = 10;
                    column.eachCell({
                        includeEmpty: true
                    }, cell => {
                        const columnLength = cell.value ? cell.value.toString().length : 0;
                        if (columnLength > maxLength) {
                            maxLength = columnLength;
                        }
                    });
                    column.width = maxLength + 2;
                });

                workbook.xlsx.writeBuffer().then(buffer => {
                    const blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = fileName + '.xlsx'; 
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal mengekspor data.', 'error');
            });
    });
</script>
@endpush