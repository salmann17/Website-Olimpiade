@extends('layout.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75]">
    <div class="bg-transparent p-8 rounded-2xl shadow-2xl w-full max-w-sm text-white text-center space-y-5">
        <div>
            <h2 class="text-lg font-bold">Tambah Peserta Baru</h2>
            <p class="text-sm text-white/80">Silahkan isi data peserta</p>
        </div>

        <form action="{{ route('admin.peserta.store') }}" method="POST" class="text-left space-y-4">
            @csrf
            <div>
                <label for="username" class="block mb-1">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none @error('username') border-2 border-red-500 @enderror"
                    value="{{ old('username') }}">
                @error('username')
                <p class="text-red-200 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fullname" class="block mb-1">Fullname</label>
                <input type="text" name="fullname" id="fullname" required
                    class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none @error('fullname') border-2 border-red-500 @enderror"
                    value="{{ old('fullname') }}">
                @error('fullname')
                <p class="text-red-200 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none @error('password') border-2 border-red-500 @enderror">
                @error('password')
                <p class="text-red-200 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block mb-1">Cek Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none">
            </div>

            <button type="submit" class="w-full py-2 bg-gray-600 text-white hover:text-[#5f27cd] font-semibold rounded-lg hover:bg-white transition">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection