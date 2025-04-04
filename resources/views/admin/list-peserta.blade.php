@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75] py-8 px-4">
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">List Peserta</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing: 0;">
                <thead class="bg-gradient-to-br from-[#48dbfb] to-[#5f27cd]">
                    <tr>
                        <th class="px-4 py-2 text-left text-white font-medium">No</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Username</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Nama Lengkap</th>
                        <th class="px-4 py-2 text-left text-white font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peserta as $index => $user)
                    <tr class="odd:bg-white even:bg-gray-200 hover:bg-gray-400 transition-colors">
                        <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-b">{{ $user->username }}</td>
                        <td class="px-4 py-2 border-b">{{ $user->fullname }}</td>
                        <td class="px-4 py-2 border-b">
                            <button
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition-colors"
                                onclick="editPeserta({{ $user->id }}, '{{ $user->username }}', '{{ $user->fullname }}')">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function editPeserta(userId, username, fullname) {
        Swal.fire({
            title: 'Edit Peserta',
            html: `
        <div class="text-left">
          <label class="block text-sm mb-1">Username</label>
          <input id="swal-username" type="text" class="swal2-input mb-3" value="${username}">

          <label class="block text-sm mb-1">Nama Lengkap</label>
          <input id="swal-fullname" type="text" class="swal2-input mb-3" value="${fullname}">

          <label class="block text-sm mb-1">Password (opsional)</label>
          <input id="swal-password" type="password" class="swal2-input mb-3 w-80" placeholder="Kosongkan jika tidak diganti">
        </div>
      `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                const swalUsername = document.getElementById('swal-username').value;
                const swalFullname = document.getElementById('swal-fullname').value;
                const swalPassword = document.getElementById('swal-password').value;

                if (!swalUsername || !swalFullname) {
                    Swal.showValidationMessage('Username dan Nama Lengkap tidak boleh kosong');
                    return false;
                }

                return {
                    username: swalUsername,
                    fullname: swalFullname,
                    password: swalPassword
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const data = result.value;
                fetch("{{ route('admin.peserta.update') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            id: userId,
                            username: data.username,
                            fullname: data.fullname,
                            password: data.password
                        })
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengupdate data.'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan server.'
                        });
                    });
            }
        });
    }
</script>
@endpush