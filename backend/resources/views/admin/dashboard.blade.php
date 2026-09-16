@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Dashboard Sistem Peminjaman')

@section('content')

    {{-- Selamat Datang --}}
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">

        Selamat datang,
        <strong class="font-semibold">
            {{ auth()->user()->name }}
        </strong>!

        Anda login sebagai hak akses

        <span class="uppercase font-bold text-emerald-900">
            {{ auth()->user()->role }}
        </span>.

    </div>


{{-- Statistik Dashboard --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

    {{-- Total Alat --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Alat
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalAlat }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                📦
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200
                flex flex-col sm:flex-row
                sm:items-center sm:justify-between
                gap-3">

        <div class="min-w-0">
            <h2 class="text-base font-semibold text-gray-800">
                Aktivitas Terbaru
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Aktivitas terakhir yang terjadi di dalam sistem
            </p>
        </div>

        <div class="w-10 h-10 shrink-0 bg-emerald-100 text-emerald-600
                    rounded-lg flex items-center justify-center">
            <i class="bi bi-clock-history text-lg"></i>
        </div>

    </div>


    {{-- Daftar Aktivitas --}}
    <div class="divide-y divide-gray-100">

        @forelse ($logs as $log)

            <div class="px-4 sm:px-6 py-4 flex items-start gap-3 sm:gap-4
                        hover:bg-gray-50 transition">

                {{-- Icon --}}
                <div class="w-10 h-10 rounded-full bg-gray-100
                            flex items-center justify-center shrink-0">
                    <i class="bi bi-person-check text-gray-500"></i>
                </div>

                {{-- Isi --}}
                <div class="flex-1 min-w-0">

                    <p class="text-sm text-gray-700 break-words">
                        <span class="font-semibold text-gray-800">
                            {{ $log->user->name ?? 'Sistem' }}
                        </span>

                        {{ $log->aktivitas }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </p>

                </div>

            </div>

        @empty

            <div class="px-4 sm:px-6 py-10 text-center">
                <i class="bi bi-inbox text-3xl text-gray-300"></i>

                <p class="mt-2 text-sm text-gray-500">
                    Belum ada aktivitas terbaru.
                </p>
            </div>

        @endforelse

    </div>

</div>


    {{-- Total Pengguna --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Pengguna
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalUsers }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                👥
            </div>
        </div>
    </div>


    {{-- Total Kategori --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Kategori
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalKategori }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                📂
            </div>
        </div>
    </div>


    {{-- Total Peminjaman --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Peminjaman
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalPeminjaman }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                📋
            </div>
        </div>
    </div>


    {{-- Peminjaman Aktif --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Peminjaman Aktif
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalPeminjamanAktif }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                🔄
            </div>
        </div>
    </div>


    {{-- Peminjaman Selesai --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Peminjaman Selesai
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalPeminjamanSelesai }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                ✅
            </div>
        </div>
    </div>


    {{-- Peminjaman Terlambat --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Peminjaman Terlambat
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalPeminjamanTerlambat }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                ⏰
            </div>
        </div>
    </div>


    {{-- Total Denda --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Denda
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    Rp {{ number_format($totalDenda, 0, ',', '.') }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                💰
            </div>
        </div>
    </div>
</div>

{{-- Statistik Tambahan --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 max-w-2xl mx-auto">

    {{-- Total Alat Rusak --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Alat Rusak
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalAlatRusak }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-xl">
                🛠️
            </div>
        </div>
    </div>


    {{-- Stok Tersedia --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">
                    Stok Tersedia
                </p>

                <h3 class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $totalStokTersedia }}
                </h3>
            </div>

            <div class="w-11 h-11 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center text-xl">
                📦
            </div>
        </div>
    </div>

</div>

@endsection