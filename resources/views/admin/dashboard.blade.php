@extends('layout.app')

@section('content')
<div class="p-6">
    <!-- Baris Pertama: Statistik Warning Count -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach(['Babak Penyisihan 1', 'Babak Penyisihan 2', 'Babak Semifinal'] as $stage)
        <div class="bg-transparent rounded-lg p-4 shadow-lg">
            <h3 class="text-lg font-semibold mb-4 text-black">{{ $stage }} - Warning Count</h3>
            <canvas id="warningChart{{ $loop->index }}" class="w-full h-64"></canvas>
        </div>
        @endforeach
    </div>

    <!-- Baris Kedua: Statistik Nilai -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(['Babak Penyisihan 1', 'Babak Penyisihan 2', 'Babak Semifinal'] as $stage)
        <div class="bg-transparent rounded-lg p-4 shadow-lg">
            <h3 class="text-lg font-semibold mb-4 text-black">{{ $stage }} - Distribusi Nilai</h3>
            <canvas id="scoreChart{{ $loop->index }}" class="w-full h-64"></canvas>
        </div>
        @endforeach
    </div>
</div>

<input type="hidden" id="warning-data" value="{{ htmlspecialchars(json_encode($warningData), ENT_QUOTES, 'UTF-8') }}">
<input type="hidden" id="score-data" value="{{ htmlspecialchars(json_encode($scoreData), ENT_QUOTES, 'UTF-8') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const warningData = JSON.parse(document.getElementById('warning-data').value);
    const scoreData = JSON.parse(document.getElementById('score-data').value);

    const warningColors = ['#06FF00', '#FFFF00', '#FF1700'];
    const scoreColors = '#3DB2FF';

    warningData.forEach((data, index) => {
        new Chart(document.getElementById(`warningChart${index}`), {
            type: 'pie',
            data: {
                labels: ['Aman', 'Peringatan', 'Diskualifikasi'],
                datasets: [{
                    data: data,
                    backgroundColor: warningColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#000' } }
                }
            }
        });
    });

    scoreData.forEach((data, index) => {
        new Chart(document.getElementById(`scoreChart${index}`), {
            type: 'bar',
            data: {
                labels: Array.from({length: 10}, (_, i) => `${i*10}-${(i+1)*10}`),
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: data,
                    backgroundColor: scoreColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#000' } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#000' },
                        title: { display: true, text: 'Jumlah Peserta', color: '#000' }
                    },
                    x: {
                        ticks: { color: '#000' },
                        title: { display: true, text: 'Rentang Nilai', color: '#000' }
                    }
                }
            }
        });
    });
</script>

@endsection
