<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ujian Dimulai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-white min-h-screen">
    <img src="{{ asset('bi.png') }}" alt="BI Logo" class="absolute top-0 left-0 m-4 h-[3.5rem] opacity-80">
    <img src="{{ asset('genbi.png') }}" alt="GenBI Logo" class="absolute top-0 right-0 m-4 h-[3.5rem] opacity-80">


    <div id="exam-content" class="p-6 pt-20">
        <h1 class="text-center text-3xl font-bold text-white my-4"></h1>
        <div id="exam-rules" class="max-w-3xl mx-auto mt-8 mb-6 p-6 bg-[#002855] text-white rounded-2xl shadow-2xl shadow-black/40">
            <h2 class="text-2xl font-bold mb-4 text-center text-white">Aturan Ujian</h2>
            <ol class="list-decimal list-inside space-y-2 text-white text-xl leading-relaxed">
                <li class="ml-4">UJIAN HARUS DISELESAIKAN DALAM WAKTU YANG TELAH DITENTUKAN TANPA PERPANJANGAN WAKTU.</li>
                <li class="ml-4">DILARANG MEMBUKA TAB BARU ATAU BERPINDAH JENDELA SELAMA UJIAN BERLANGSUNG.</li>
                <li class="ml-4">PESERTA UJIAN WAJIB MENGAKTIFKAN MODE LAYAR PENUH SAAT MEMULAI UJIAN.</li>
                <li class="ml-4">SETIAP PELANGGARAN AKAN TERCATAT DAN DAPAT MENYEBABKAN DISKUALIFIKASI.</li>
                <li class="ml-4">GUNAKAN PERANGKAT YANG STABIL DAN TERHUBUNG KE INTERNET DENGAN BAIK.</li>
                <li class="ml-4">JAWABAN YANG SUDAH DIKIRIM TIDAK DAPAT DIUBAH KEMBALI.</li>
                <li class="ml-4">DILARANG KERAS BEKERJA SAMA DENGAN PESERTA LAIN ATAU PIHAK KETIGA.</li>
                <li class="ml-4">SEMUA JAWABAN AKAN DIPANTAU DAN DIPERIKSA MENGGUNAKAN SISTEM ANTI-KECURANGAN OTOMATIS.</li>
            </ol>
        </div>
        <div class="flex justify-center">
            <button id="start-fullscreen" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded shadow-lg">
                Mulai Ujian
            </button>
        </div>

        <!-- Countdown Timer  -->
        <div class="flex items-center justify-center">
            <div id="countdown-timer" class="text-center py-4 bg-blue-600 text-white rounded-full px-4 text-3xl transition-colors duration-300 hidden">
                Sisa Waktu: <span id="timer"></span>
            </div>
        </div>


        <!-- Container Soal (hidden sampai Mulai Ujian ditekan) -->
        <div id="questions-container" class="mt-6 hidden">
            <!-- Soal-soal akan di-render di sini -->
            @foreach($questions as $question)
            <div class="question hidden" id="question-{{ $loop->index }}">
                <h2 class="font-bold text-black mb-2 text-xl">Soal {{ $loop->iteration }}</h2>
                @if($question->id == 76 && $schedule->id == 1)
                <div class="text-center mb-4">
                    <img src="{{ asset('75k.jpg') }}" alt="Gambar 75k" class="mx-auto w-1/4">
                </div>
                @endif
                <p class="mb-4 text-black text-2xl">{!! nl2br(e($question->question)) !!}</p>
                @if($question->type === 'multiple_choice')
                <form class="answer-form" data-question-id="{{ $question->id }}">
                    @if($question->pilihan_a)
                    <div class="mb-2">
                        <label class="text-black text-xl ">
                            <input type="radio" name="answer-{{ $question->id }}" value="a" class="mr-2">
                            {{ $question->pilihan_a }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_b)
                    <div class="mb-2">
                        <label class="text-black text-xl ">
                            <input type="radio" name="answer-{{ $question->id }}" value="b" class="mr-2">
                            {{ $question->pilihan_b }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_c)
                    <div class="mb-2">
                        <label class="text-black text-xl ">
                            <input type="radio" name="answer-{{ $question->id }}" value="c" class="mr-2">
                            {{ $question->pilihan_c }}
                        </label>
                    </div>
                    @endif
                    @if($question->pilihan_d)
                    <div class="mb-2">
                        <label class="text-black text-xl">
                            <input type="radio" name="answer-{{ $question->id }}" value="d" class="mr-2">
                            {{ $question->pilihan_d }}
                        </label>
                    </div>
                    @endif
                </form>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Navigasi Soal -->
        <div id="navigation" class="flex justify-start mt-6 hidden">
            <button id="next-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded shadow">Soal Setelahnya</button>
        </div>

        <input type="hidden" id="total-soal" value="{{ count($questions) }}">
        <input type="hidden" id="duration-minutes" value="{{ $schedule->duration }}">
    </div>

    <script>
        let suppressWarningsUntil = 0;
        let examJustStarted = true;

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

            const rules = document.getElementById('exam-rules');
            if (rules) {
                rules.classList.add('hidden');
            }
            this.style.display = "none";
            examJustStarted = true;
            setTimeout(() => {
                examJustStarted = false;
            }, 2000);

            document.getElementById('questions-container').classList.remove('hidden');
            document.getElementById('countdown-timer').classList.remove('hidden');

            showQuestion(currentQuestion);

            setInterval(updateTimer, 1000);
        });


        let warnings = 0;
        const maxWarnings = 2;

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
        function forceFinish(answers) {
            return fetch("{{ route('quiz.finish', ['schedule' => $schedule->id]) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    quiz_session_id: "{{ $quizSession->id }}",
                    force: true,
                    answers: answers
                })
            }).then(response => response.json());
        }


        // Debug logging: untuk memastikan event terpicu
        function logEvent(eventName, extra = '') {
            console.log(eventName + " fired. " + extra);
        }

        let wasHidden = false;


        // Event: document visibility berubah (misalnya, tab berubah atau minimize)
        document.addEventListener("visibilitychange", function() {
            if (examJustStarted) return;
            if (document.visibilityState === "hidden") {
                wasHidden = true;
            } else if (document.visibilityState === "visible" && wasHidden) {
                wasHidden = false;
                handleWarningEvent("Anda berpindah tab atau aplikasi!");
                if (!document.fullscreenElement) {
                    handleWarningEvent("Anda keluar dari mode fullscreen!");
                }
            }
        });

        // Event: perubahan fullscreen (misalnya, user menekan ESC)
        document.addEventListener('fullscreenchange', function() {
            if (examJustStarted) return; // Abaikan perubahan fullscreen awal
            logEvent("fullscreenchange", "fullscreenElement: " + document.fullscreenElement);
            if (!document.fullscreenElement) {
                handleWarningEvent("Anda keluar dari mode fullscreen!");
            }
        });

        // Event: resize jendela (misalnya, user mengecilkan ukuran jendela)
        window.addEventListener("resize", function() {
            if (examJustStarted) return;
            logEvent("resize", "outerWidth: " + window.outerWidth + ", outerHeight: " + window.outerHeight);

            if (!document.fullscreenElement) {
                handleWarningEvent("Anda keluar dari mode fullscreen!");
            }

            if (window.outerWidth < screen.width || window.outerHeight < screen.height) {
                handleWarningEvent("Ukuran jendela telah diubah!");
            }
        });

        let examFinished = false;
        // Logika pemberian warning dan force finish
        function handleWarningEvent(message) {
            if (examFinished) return;
            if (Date.now() < suppressWarningsUntil) return;
            suppressWarningsUntil = Date.now() + 1000;

            warnings++;
            if (warnings === 1) {
                sendWarning().then(() => {
                    Swal.fire({
                        title: 'Peringatan',
                        text: message + ' Jika Anda melakukannya sekali lagi, ujian akan dianggap selesai.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        if (!document.fullscreenElement) {
                            requestFullScreen();
                        }
                    });
                });
            } else if (warnings >= 2) {
                examFinished = true;
                let finalAnswers = [];
                document.querySelectorAll('.answer-form').forEach(form => {
                    const questionId = form.getAttribute('data-question-id');
                    let answerInput = form.querySelector('input[name="answer-' + questionId + '"]:checked');
                    let answer = answerInput ? answerInput.value : '';
                    finalAnswers.push({
                        question_id: questionId,
                        answer: answer
                    });
                });
                forceFinish(finalAnswers).then((data) => {
                    console.log("Finish response: ", data);
                    Swal.fire({
                        title: 'Ujian Selesai',
                        text: 'Anda telah melanggar aturan sebanyak 2 kali. Ujian dianggap selesai.',
                        icon: 'info',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        localStorage.removeItem('examState');
                        window.close();
                    });
                }).catch(err => {
                    console.error("Force finish error: ", err);
                });

            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", restoreExamState);

        // Saat quiz dimulai:
        if (!localStorage.getItem('quiz_session_id')) {
            localStorage.setItem('quiz_session_id', "{{ $quizSession->id }}");
        }

        const totalSoal = parseInt(document.getElementById('total-soal').value, 10);
        let durationMinutes = parseInt(document.getElementById('duration-minutes').value, 10);
        let currentQuestion = 0;
        let answerStatus = Array(totalSoal).fill(false);
        let remainingSeconds = durationMinutes * 60;

        // Fungsi untuk menyimpan state ujian ke localStorage
        function saveExamState() {
            const state = {
                currentQuestion: currentQuestion,
                remainingSeconds: remainingSeconds,
                answerStatus: answerStatus,
                answers: {},
                warnings: warnings
            };
            document.querySelectorAll('.answer-form').forEach(form => {
                const qId = form.getAttribute('data-question-id');
                const selected = form.querySelector('input[name="answer-' + qId + '"]:checked');
                state.answers[qId] = selected ? selected.value : '';
            });
            localStorage.setItem('examState', JSON.stringify(state));
        }


        // Fungsi untuk memulihkan state ujian dari localStorage
        function restoreExamState() {
            const savedState = localStorage.getItem('examState');
            if (savedState) {
                try {
                    const state = JSON.parse(savedState);
                    if (state.currentQuestion !== undefined) currentQuestion = state.currentQuestion;
                    if (state.remainingSeconds !== undefined) remainingSeconds = state.remainingSeconds;
                    if (state.answerStatus && state.answerStatus.length === totalSoal) {
                        answerStatus = state.answerStatus;
                    }
                    if (state.warnings !== undefined) {
                        warnings = state.warnings;
                    }
                    if (state.answers) {
                        for (const qId in state.answers) {
                            const radio = document.querySelector('input[name="answer-' + qId + '"][value="' + state.answers[qId] + '"]');
                            if (radio) {
                                radio.checked = true;
                            }
                        }
                    }
                } catch (e) {
                    console.error("Gagal memulihkan state ujian:", e);
                }
            }
        }

        // Fungsi untuk menampilkan soal sesuai index
        function showQuestion(index) {
            document.querySelectorAll('.question').forEach(el => el.classList.add('hidden'));

            document.getElementById('question-' + index).classList.remove('hidden');
            updateNavButtons();
            saveExamState();

        }

        // Fungsi untuk update tampilan tombol navigasi
        function updateNavButtons() {
            let activeForm = document.querySelector(`#question-${currentQuestion} .answer-form`);
            let answered = false;
            if (activeForm) {
                let selected = activeForm.querySelector('input:checked');
                if (selected) {
                    answered = true;
                }
            }
            if (answered) {
                document.getElementById('navigation').classList.remove('hidden');
            } else {
                document.getElementById('navigation').classList.add('hidden');
            }
            document.getElementById('next-btn').textContent = (currentQuestion === totalSoal - 1) ? 'Submit Ujian' : 'Soal Setelahnya';
        }

        document.getElementById('next-btn').addEventListener('click', function() {
            if (currentQuestion === totalSoal - 1) {
                Swal.fire({
                    title: 'Submit Ujian',
                    text: 'Apakah anda yakin ingin menyelesaikan ujian?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, submit',
                    confirmButtonColor: '#3085d6',
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

        if (document.getElementById('prev-btn')) {
            document.getElementById('prev-btn').style.display = 'none';
        }
        if (document.getElementById('daftar-soal-btn')) {
            document.getElementById('daftar-soal-btn').style.display = 'none';
        }

        // Fungsi untuk submit ujian (digunakan saat waktu habis atau submit manual)
        function submitExam(autoSubmit) {
            let finalAnswers = [];
            document.querySelectorAll('.answer-form').forEach(form => {
                const questionId = form.getAttribute('data-question-id');
                let answerInput = form.querySelector('input[name="answer-' + questionId + '"]:checked');
                let answer = answerInput ? answerInput.value : '';
                finalAnswers.push({
                    question_id: questionId,
                    answer: answer
                });
            });

            console.log("Submitting exam with answers:", finalAnswers);

            fetch("{{ route('quiz.finish', ['schedule' => $schedule->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quiz_session_id: "{{ $quizSession->id }}",
                        answers: finalAnswers
                    })
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Finish endpoint returned:", data);
                    Swal.fire({
                        title: autoSubmit ? 'Waktu Habis' : 'Ujian Disubmit',
                        text: autoSubmit ?
                            'Waktu ujian telah habis. Jawaban anda telah disimpan.' : 'Jawaban anda telah disimpan.',
                        icon: 'info'
                    }).then(() => {
                        localStorage.removeItem('examState');
                        window.close(); // Tutup jendela ujian setelah submit
                    });
                })
                .catch(err => {
                    console.error("Error in submitExam:", err);
                });
        }

        // Countdown Timer
        function updateTimer() {
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;

            const timerElement = document.getElementById('timer');
            const countdownElement = document.getElementById('countdown-timer');

            timerElement.textContent = `${minutes} menit ${seconds} detik`;

            if (remainingSeconds < 300) {
                countdownElement.classList.remove('bg-blue-600');
                countdownElement.classList.add('bg-red-600');
            } else {
                countdownElement.classList.remove('bg-red-600');
                countdownElement.classList.add('bg-blue-600');
            }

            if (remainingSeconds <= 0) {
                submitExam(true);
            } else {
                remainingSeconds--;
                saveExamState();
            }
        }

        function startCountdown(durationInMinutes) {
            remainingSeconds = durationInMinutes * 60;
            updateTimer();

            const interval = setInterval(() => {
                updateTimer();
                if (remainingSeconds <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        }
        // --- Event ketika user mengklik tombol "Mulai Ujian" ---
        document.getElementById("start-fullscreen").addEventListener("click", function() {
            document.getElementById('questions-container').classList.remove('hidden');
            document.getElementById('navigation').classList.remove('hidden');
            document.getElementById('countdown-timer').classList.remove('hidden');

        });


        const quizAnswerRoute = "{{ route('quiz.answer', ['schedule' => $schedule->id]) }}";
        const csrfToken = "{{ csrf_token() }}";
        const quizSessionId = "{{ $quizSession->id }}";

        document.querySelectorAll('.answer-form').forEach(form => {
            form.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                let answerElement = this.querySelector('input[name="answer-' + questionId + '"]:checked');
                let answer = answerElement ? answerElement.value : '';
                answerStatus[currentQuestion] = true;
                saveExamState();

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
                        document.getElementById('navigation').classList.remove('hidden');
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
</body>

</html>