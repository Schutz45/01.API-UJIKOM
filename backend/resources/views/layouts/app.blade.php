<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
<!-- Membuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen min-w-0: overflow-hidden">

<!-- SIDEBAR -->
<aside class="w-64 shrink-0 bg-gray-900 text-white flex flex-col hidden md:flex overflow-hidden">

    {{-- JUDUL PANEL --}}
    <div class="px-5 py-5 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center">
                <i class="bi bi-tools text-xl"></i>
            </div>

            <div>
                @if(auth()->user()->role === 'admin')
                    <h1 class="text-lg font-bold tracking-wide">
                        PANEL ADMIN
                    </h1>
                @elseif(auth()->user()->role === 'petugas')
                    <h1 class="text-lg font-bold tracking-wide">
                        PANEL PETUGAS
                    </h1>
                @endif

                <p class="text-xs text-gray-500">
                    Sistem Peminjaman Alat
                </p>
            </div>
        </div>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

        {{-- ================= ADMIN ================= --}}
        @if(auth()->user()->role === 'admin')

            <p class="px-4 pt-1 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Menu Utama
            </p>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.dashboard')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <p class="px-4 pt-5 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Data Master
            </p>

            {{-- User --}}
            <a href="{{ route('admin.user.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.user*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-people"></i>
                <span>Kelola User</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.kategori.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.kategori*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-tags"></i>
                <span>Kelola Kategori</span>
            </a>

            {{-- Alat --}}
            <a href="{{ route('admin.alat.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.alat*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-tools"></i>
                <span>Kelola Alat</span>
            </a>

            <p class="px-4 pt-5 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Transaksi
            </p>

            {{-- Peminjaman --}}
            <a href="{{ route('admin.peminjaman.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.peminjaman*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-clipboard-check"></i>
                <span>Kelola Peminjaman</span>
            </a>

            {{-- Pengembalian --}}
            <a href="{{ route('admin.pengembalian.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.pengembalian*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-arrow-return-left"></i>
                <span>Kelola Pengembalian</span>
            </a>

            <p class="px-4 pt-5 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Sistem
            </p>

            {{-- Log --}}
            <a href="{{ route('admin.log_aktivitas.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('admin.log_aktivitas*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>

        @endif


        {{-- ================= PETUGAS ================= --}}
        @if(auth()->user()->role === 'petugas')

            <p class="px-4 pt-1 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Menu Petugas
            </p>

            {{-- Persetujuan --}}
            <a href="{{ route('petugas.peminjaman.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('petugas.peminjaman*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-check2-square"></i>
                <span>Persetujuan Peminjaman</span>
            </a>

            {{-- Pengembalian --}}
            <a href="{{ route('petugas.pengembalian.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('petugas.pengembalian*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-box-arrow-in-left"></i>
                <span>Pemantauan Pengembalian</span>
            </a>

            {{-- Laporan --}}
            <a href="{{ route('petugas.laporan.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition
                {{ request()->routeIs('petugas.laporan*')
                    ? 'bg-emerald-600 text-white shadow'
                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <i class="bi bi-file-earmark-text"></i>
                <span>Cetak Laporan</span>
            </a>

        @endif

    </nav>


    {{-- USER INFO --}}
    <div class="px-4 py-4 border-t border-gray-800">

        <div class="flex items-center gap-3">

            <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
                <i class="bi bi-person text-gray-300"></i>
            </div>

            <div class="min-w-0">
                <p class="text-xs text-gray-500">
                    Login sebagai
                </p>

                <p class="text-sm text-white font-semibold truncate">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-gray-500 capitalize">
                    {{ auth()->user()->role }}
                </p>
            </div>

        </div>

    </div>

</aside>

<!-- MAIN CONTENT CONTAINER -->
 <div class="flex-1 min-w-0 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 h-16 shrink-0 flex items-center justify-between px-4 sm:px-6 z-10">

        {{-- Judul Halaman --}}
        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                @yield('header-title', 'Dashboard')
            </h1>
        </div>

        {{-- Informasi User + Logout --}}
        <div class="flex items-center gap-4">

            {{-- User --}}
            <div class="flex items-center gap-3">

                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="bi bi-person-fill text-emerald-600"></i>
                </div>

                <div class="hidden sm:block leading-tight">
                    <div class="text-sm font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-gray-500 capitalize">
                        {{ auth()->user()->role }}
                    </div>
                </div>

            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg
                        text-sm font-medium text-gray-600
                        hover:bg-red-50 hover:text-red-600
                        transition">

                    <i class="bi bi-box-arrow-right"></i>

                    <span class="hidden sm:inline">
                        Logout
                    </span>

                </button>
            </form>

        </div>

    </header>

    <!-- KONTEN UTAMA HALAMAN -->
     <main class="flex-1 min-w-0 overflow-y-auto p-4 sm:p-6">
        @yield('content')
    </main>
</div>
    
</body>
</html>