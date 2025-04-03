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
</body>

</html>