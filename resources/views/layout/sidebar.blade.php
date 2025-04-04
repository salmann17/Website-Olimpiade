@php
use Illuminate\Support\Facades\Auth;
$user = Auth::user(); // Ambil data user yang sedang login
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<div class="w-64 min-h-screen bg-gradient-to-b from-[#5f27cd] to-[#48dbfb] text-white p-6 shadow-lg flex flex-col justify-between">
    {{-- Header --}}
    <div>
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-10">
            <!-- <button id="sidebarToggle" class="text-white">
                <i class="fas fa-bars"></i>
            </button> -->
        </div>

        {{-- Greeting --}}
        <div class="mb-6">
            <p class="text-sm">Selamat datang,</p>
            <h2 class="text-lg font-semibold">
                {{ $user ? $user->fullname : 'Guest' }}!
            </h2>
        </div>

        {{-- Navigation --}}
        <nav class="space-y-2">
            <a href="{{ route(($user && $user->role === 'admin') ? 'admin.dashboard' : 'peserta.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-white/20 transition">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>

            {{-- Tautan khusus Admin --}}
            @if($user && $user->role === 'admin')
            <a href=""
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-white/20 transition">
                <i class="fa-solid fa-eye"></i> Pemantauan Ujian
            </a>
            <a href="{{ route('admin.peserta.list') }}"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-white/20 transition">
                <i class="fa-solid fa-list"></i> List Peserta
            </a>
            <a href="{{ route('admin.peserta.create') }}"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-white/20 transition">
                <i class="fas fa-user-plus w-5"></i> Tambah Peserta
            </a>
            @endif

            {{-- Tautan khusus Peserta --}}
            @if($user && $user->role === 'peserta')
            <a href="{{ route('peserta.riwayat') }}"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-white/20 transition">
                <i class="fas fa-history w-5"></i> Riwayat Ujian
            </a>
            @endif
        </nav>
    </div>

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" class="mt-10">
        @csrf
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 rounded transition">
            <i class="fas fa-sign-out-alt w-5"></i> Logout
        </button>
    </form>
</div>