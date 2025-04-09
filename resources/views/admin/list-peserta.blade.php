@extends('layout.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-[#f6f9fc] via-[#b6cbe0] to-[#002855] py-8 px-4">
    <div class="max-w-6xl mx-auto bg-transparent rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-[#004a80]">List Peserta</h2>

        <!-- Search Form -->
        <form action="{{ route('admin.peserta.list') }}" method="GET" class="mb-4 flex gap-2">
            <input type="text" name="search" placeholder="Cari username..." value="{{ $search }}"
                class="px-3 py-2 border rounded flex-1"
                onkeydown="if(event.key==='Enter') this.form.submit()">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Search</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full border-separate" style="border-spacing:0">
                <thead class="bg-[#004a80]">
                    <tr>
                        <th class="px-4 py-2 text-white">No</th>
                        <th class="px-4 py-2 text-white">Username</th>
                        <th class="px-4 py-2 text-white">Nama Lengkap</th>
                        <th class="px-4 py-2 text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peserta as $index => $user)
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-200">
                        <td class="px-4 py-2 border">
                            {{ ($peserta->currentPage() - 1) * $peserta->perPage() + $index + 1 }}
                        </td>
                        <td class="px-4 py-2 border">{{ $user->username }}</td>
                        <td class="px-4 py-2 border">{{ $user->fullname }}</td>
                        <td class="px-4 py-2 border">
                            <button
                                class="bg-[#004a80] hover:bg-blue-300 text-white px-3 py-1 rounded"
                                onclick="editPeserta({{ $user->id }}, '{{ $user->username }}', '{{ $user->fullname }}')">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $peserta->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
            confirmButtonColor: '#3085d6', 
            cancelButtonColor: '#d33', 
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
                                confirmButtonColor: '#3085d6', 
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