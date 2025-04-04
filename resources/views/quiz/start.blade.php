<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ujian Dimulai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <div id="exam-content" class="p-4">
        <h1 class="text-center text-3xl font-bold my-4">Ujian Dimulai</h1>
        <!-- Tombol untuk memulai fullscreen -->
        <button id="start-fullscreen" class="bg-blue-600 text-white px-4 py-2 rounded">
            Mulai Ujian
        </button>

        <!-- Countdown Timer (akan muncul setelah user klik Mulai Ujian) -->
        <div id="countdown-timer" class="text-center py-2 bg-blue-600 text-white mt-4 hidden">
            Sisa Waktu: <span id="timer"></span>
        </div>

        <!-- Container Soal (hidden sampai Mulai Ujian ditekan) -->
        <div id="questions-container" class="mt-4 hidden">
            <!-- Soal-soal akan di-render di sini -->
            @foreach($questions as $question)
            <div class="question hidden" id="question-{{ $loop->index }}">
                <h2 class="font-bold mb-2">Soal {{ $loop->iteration }}</h2>
                <p class="mb-4">{!! nl2br(e($question->question)) !!}</p>
                @if($question->type === 'multiple_choice')
                <form class="answer-form" data-question-id="{{ $question->id }}">
                    @if($question->pilihan_a)
                    <div>
                        <label>
                            <input type="radio" name="answer-{{ $question->id }}" value="a">
                            {{ $question->pilihan_a }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_b)
                    <div>
                        <label>
                            <input type="radio" name="answer-{{ $question->id }}" value="b">
                            {{ $question->pilihan_b }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_c)
                    <div>
                        <label>
                            <input type="radio" name="answer-{{ $question->id }}" value="c">
                            {{ $question->pilihan_c }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_d)
                    <div>
                        <label>
                            <input type="radio" name="answer-{{ $question->id }}" value="d">
                            {{ $question->pilihan_d }}
                        </label>
                    </div>
                    @endif
                    <input type="hidden" id="total-soal" value="{{ count($questions) }}">
                    <input type="hidden" id="duration-minutes" value="{{ $schedule->duration }}">
                </form>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Navigasi Soal -->
        <div id="navigation" class="flex justify-between mt-4 hidden">
            <button id="prev-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Soal Sebelumnya</button>
            <button id="daftar-soal-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Daftar Soal</button>
            <button id="next-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Soal Setelahnya</button>
        </div>
        <!-- Konten ujian lainnya (soal, timer, dsb) dapat diletakkan di sini -->
    </div>

    <script>
        // Fungsi untuk meminta fullscreen
        function requestFullScreen() {
            const docElm = document.documentElement;
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            } else if (docElm.webkitRequestFullscreen) {
                docElm.webkitRequestFullscreen();
            } else if (docElm.msRequestFullscreen) {
                docElm.msRequestFullscreen();
            }
        }

        // Pastikan fullscreen dipicu oleh aksi klik pengguna
        document.getElementById("start-fullscreen").addEventListener("click", function() {
            requestFullScreen();
            // Sembunyikan tombol setelah diklik
            this.style.display = "none";
        });

        // Counter untuk warning
        let warnings = 0;
        const maxWarnings = 2; // Pada warning ke-2, ujian akan force finish

        // Fungsi AJAX untuk meng-update warning (warning pertama)
        function sendWarning() {
            return fetch("{{ route('quiz.warning', ['schedule' => $schedule->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quiz_session_id: "{{ $quizSession->id }}"
                    })
                })
                .then(response => response.json());
        }

        // Fungsi AJAX untuk force finish (warning kedua)
        function forceFinish() {
            return fetch("{{ route('quiz.finish', ['schedule' => $schedule->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quiz_session_id: "{{ $quizSession->id }}"
                    })
                })
                .then(response => response.json());
        }

        // Debug logging: untuk memastikan event terpicu
        function logEvent(eventName, extra = '') {
            console.log(eventName + " fired. " + extra);
        }

        // Event: document visibility berubah (misalnya, tab berubah atau minimize)
        document.addEventListener("visibilitychange", function() {
            logEvent("visibilitychange", "State: " + document.visibilityState);
            if (document.visibilityState === "hidden") {
                handleWarningEvent("Anda berpindah tab atau meminimalkan jendela!");
            }
        });

        // Event: jendela kehilangan fokus
        window.addEventListener("blur", function() {
            logEvent("window blur");
            handleWarningEvent("Jendela kehilangan fokus!");
        });

        // Event: perubahan fullscreen (misalnya, user menekan ESC)
        document.addEventListener('fullscreenchange', function() {
            logEvent("fullscreenchange", "fullscreenElement: " + document.fullscreenElement);
            if (!document.fullscreenElement) {
                handleWarningEvent("Anda keluar dari mode fullscreen!");
            }
        });

        // Event: resize jendela (misalnya, user mengecilkan ukuran jendela)
        window.addEventListener("resize", function() {
            logEvent("resize", "outerWidth: " + window.outerWidth + ", outerHeight: " + window.outerHeight);
            // Jika ukuran jendela berkurang dari ukuran layar penuh, anggap sebagai pelanggaran
            if (window.outerWidth < screen.width || window.outerHeight < screen.height) {
                handleWarningEvent("Ukuran jendela telah diubah!");
            }
        });
        let examFinished = false;
        // Logika pemberian warning dan force finish
        function handleWarningEvent(message) {
            if (examFinished) return; // kalau sudah finished, jangan eksekusi lagi

            warnings++;
            if (warnings === 1) {
                sendWarning().then(() => {
                    Swal.fire({
                        title: 'Peringatan',
                        text: message + ' Jika Anda melakukannya sekali lagi, ujian akan dianggap selesai.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Coba kembali minta fullscreen jika user belum di mode fullscreen
                        if (!document.fullscreenElement) {
                            requestFullScreen();
                        }
                    });
                });
            } else if (warnings >= 2) {
                examFinished = true;
                forceFinish().then((data) => {
                    console.log("Finish response: ", data);
                    Swal.fire({
                        title: 'Ujian Selesai',
                        text: 'Anda telah melanggar aturan sebanyak 2 kali. Ujian dianggap selesai.',
                        icon: 'info'
                    }).then(() => {
                        window.close();
                    });
                }).catch(err => {
                    console.error("Force finish error: ", err);
                });

            }
        }
    </script>

    <script>
        const totalSoal = parseInt(document.getElementById('total-soal').value, 10);
        let durationMinutes = parseInt(document.getElementById('duration-minutes').value, 10);
        let currentQuestion = 0;
        let answerStatus = Array(totalSoal).fill(false);
        let remainingSeconds = durationMinutes * 60;



        // Fungsi untuk menampilkan soal sesuai index
        function showQuestion(index) {
            // Sembunyikan semua soal
            document.querySelectorAll('.question').forEach(el => el.classList.add('hidden'));
            // Tampilkan soal dengan index tertentu
            document.getElementById('question-' + index).classList.remove('hidden');
            updateNavButtons();
        }

        // Fungsi untuk update tampilan tombol navigasi
        function updateNavButtons() {
            // Sembunyikan tombol "Soal Sebelumnya" jika di soal pertama
            document.getElementById('prev-btn').style.display = (currentQuestion === 0) ? 'none' : 'inline-block';
            // Ubah teks tombol "Soal Setelahnya" menjadi "Submit Ujian" jika di soal terakhir
            document.getElementById('next-btn').textContent = (currentQuestion === totalSoal - 1) ? 'Submit Ujian' : 'Soal Setelahnya';
        }

        // Event listener untuk tombol navigasi
        document.getElementById('prev-btn').addEventListener('click', function() {
            if (currentQuestion > 0) {
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        });

        document.getElementById('next-btn').addEventListener('click', function() {
            if (currentQuestion === totalSoal - 1) {
                // Popup konfirmasi submit ujian
                Swal.fire({
                    title: 'Submit Ujian',
                    text: 'Apakah anda yakin ingin menyelesaikan ujian?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, submit',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitExam(false);
                    }
                });
            } else {
                currentQuestion++;
                showQuestion(currentQuestion);
            }
        });

        // Tombol "Daftar Soal" untuk menampilkan popup daftar soal
        document.getElementById('daftar-soal-btn').addEventListener('click', function() {
            let htmlContent = '<div class="grid grid-cols-5 gap-2">';
            for (let i = 0; i < totalSoal; i++) {
                let bgColor = answerStatus[i] ? 'bg-green-400' : 'bg-white';
                htmlContent += `<div class="p-2 border rounded cursor-pointer ${bgColor}" onclick="jumpToQuestion(${i})">${i+1}</div>`;
            }
            htmlContent += '</div>';
            Swal.fire({
                title: 'Daftar Soal',
                html: htmlContent,
                showConfirmButton: false
            });
        });

        // Fungsi lompat ke soal tertentu
        function jumpToQuestion(index) {
            currentQuestion = index;
            showQuestion(currentQuestion);
            Swal.close();
        }

        // Fungsi untuk submit ujian (digunakan saat waktu habis atau submit manual)
        function submitExam(autoSubmit) {
            // Panggil endpoint finish untuk mengupdate end_time dan status quiz session
            fetch("{{ route('quiz.finish', ['schedule' => $schedule->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quiz_session_id: "{{ $quizSession->id }}"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        title: autoSubmit ? 'Waktu Habis' : 'Ujian Disubmit',
                        text: autoSubmit ? 'Waktu ujian telah habis. Jawaban anda telah disimpan.' : 'Jawaban anda telah disimpan.',
                        icon: 'info'
                    }).then(() => {
                        window.close();
                    });
                })
                .catch(err => console.error(err));
        }

        // Countdown Timer
        function updateTimer() {
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            document.getElementById('timer').textContent = `${minutes} menit ${seconds} detik`;
            if (remainingSeconds <= 0) {
                submitExam(true);
            } else {
                remainingSeconds--;
            }
        }

        // --- Event ketika user mengklik tombol "Mulai Ujian" ---
        document.getElementById("start-fullscreen").addEventListener("click", function() {
            // Setelah fullscreen aktif, tampilkan container soal, navigasi, dan timer
            document.getElementById('questions-container').classList.remove('hidden');
            document.getElementById('navigation').classList.remove('hidden');
            document.getElementById('countdown-timer').classList.remove('hidden');
            // Tampilkan soal pertama
            showQuestion(currentQuestion);
            // Mulai timer setiap detik
            setInterval(updateTimer, 1000);
        });


        const quizAnswerRoute = "{{ route('quiz.answer', ['schedule' => $schedule->id]) }}";
        const csrfToken = "{{ csrf_token() }}";
        const quizSessionId = "{{ $quizSession->id }}";

        document.querySelectorAll('.answer-form').forEach(form => {
            form.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                // Ambil nilai jawaban dari input radio yang dipilih
                let answer = this.querySelector('input[name="answer-' + questionId + '"]:checked');
                if (answer) {
                    answer = answer.value;
                } else {
                    answer = '';
                }
                // Tandai soal ini sebagai sudah dijawab di array status
                answerStatus[currentQuestion] = true;

                // Kirim Ajax untuk simpan jawaban menggunakan variabel yang sudah didefinisikan
                fetch(quizAnswerRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            quiz_session_id: quizSessionId,
                            question_id: questionId,
                            answer: answer
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Jawaban tersimpan:", data);
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
</body>

</html>