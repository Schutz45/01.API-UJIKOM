@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Dashboard Sistem Peminjaman')

@section('content')

<div class="space-y-5">

    {{-- =====================================================
        STATISTIK UTAMA
    ====================================================== --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

        {{-- Total Alat --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Total Alat
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalAlat }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-blue-100
                            text-blue-600 flex items-center justify-center">
                    <i class="bi bi-box-seam text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                dari data alat
            </p>

        </div>


        {{-- Total Pengguna --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Total Pengguna
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalUsers }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-emerald-100
                            text-emerald-600 flex items-center justify-center">
                    <i class="bi bi-people-fill text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                dari data pengguna
            </p>

        </div>


        {{-- Total Kategori --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Kategori
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalKategori }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-purple-100
                            text-purple-600 flex items-center justify-center">
                    <i class="bi bi-folder-fill text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                dari data kategori
            </p>

        </div>


        {{-- Total Peminjaman --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Peminjaman
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalPeminjaman }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-orange-100
                            text-orange-500 flex items-center justify-center">
                    <i class="bi bi-clipboard-check-fill text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                total peminjaman
            </p>

        </div>


        {{-- Peminjaman Selesai --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Peminjaman Selesai
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalPeminjamanSelesai }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-cyan-100
                            text-cyan-600 flex items-center justify-center">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                total selesai
            </p>

        </div>


        {{-- Peminjaman Terlambat --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm
                    hover:shadow-md transition duration-200">

            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-xs font-medium text-slate-500">
                        Peminjaman Terlambat
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $totalPeminjamanTerlambat }}
                    </h3>
                </div>

                <div class="w-10 h-10 shrink-0 rounded-xl bg-rose-100
                            text-rose-500 flex items-center justify-center">
                    <i class="bi bi-clock-fill text-lg"></i>
                </div>

            </div>

            <p class="text-[11px] text-slate-400 mt-3">
                total terlambat
            </p>

        </div>

    </div>


    {{-- =====================================================
        BAGIAN TENGAH
    ====================================================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">


        {{-- =================================================
            STATISTIK PEMINJAMAN
        ================================================== --}}
        <div class="xl:col-span-5 bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-slate-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-base font-semibold text-slate-800">
                            Statistik Peminjaman
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            Ringkasan status peminjaman
                        </p>
                    </div>

                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600
                                flex items-center justify-center">

                        <i class="bi bi-bar-chart-fill"></i>

                    </div>

                </div>

            </div>


            {{-- Isi --}}
            <div class="p-5">

                {{-- Total --}}
                <div class="mb-5">

                    <div class="flex justify-between items-center mb-2">

                        <span class="text-sm font-medium text-slate-600">
                            Total Peminjaman
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ $totalPeminjaman }}
                        </span>

                    </div>

                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                        <div class="h-full bg-blue-500 rounded-full"
                             style="width: 100%">
                        </div>

                    </div>

                </div>


                {{-- Selesai --}}
                <div class="mb-5">

                    <div class="flex justify-between items-center mb-2">

                        <span class="text-sm font-medium text-slate-600">
                            Selesai
                        </span>

                        <span class="text-sm font-bold text-emerald-600">
                            {{ $totalPeminjamanSelesai }}
                        </span>

                    </div>

                    @php
                        $persenSelesai = $totalPeminjaman > 0
                            ? ($totalPeminjamanSelesai / $totalPeminjaman) * 100
                            : 0;
                    @endphp

                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                        <div class="h-full bg-emerald-500 rounded-full"
                             style="width: {{ $persenSelesai }}%">
                        </div>

                    </div>

                </div>


                {{-- Aktif --}}
                <div class="mb-5">

                    <div class="flex justify-between items-center mb-2">

                        <span class="text-sm font-medium text-slate-600">
                            Aktif
                        </span>

                        <span class="text-sm font-bold text-blue-600">
                            {{ $totalPeminjamanAktif }}
                        </span>

                    </div>

                    @php
                        $persenAktif = $totalPeminjaman > 0
                            ? ($totalPeminjamanAktif / $totalPeminjaman) * 100
                            : 0;
                    @endphp

                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                        <div class="h-full bg-cyan-500 rounded-full"
                             style="width: {{ $persenAktif }}%">
                        </div>

                    </div>

                </div>


                {{-- Terlambat --}}
                <div>

                    <div class="flex justify-between items-center mb-2">

                        <span class="text-sm font-medium text-slate-600">
                            Terlambat
                        </span>

                        <span class="text-sm font-bold text-rose-500">
                            {{ $totalPeminjamanTerlambat }}
                        </span>

                    </div>

                    @php
                        $persenTerlambat = $totalPeminjaman > 0
                            ? ($totalPeminjamanTerlambat / $totalPeminjaman) * 100
                            : 0;
                    @endphp

                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                        <div class="h-full bg-rose-500 rounded-full"
                             style="width: {{ $persenTerlambat }}%">
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
            AKTIVITAS TERBARU
        ================================================== --}}
        <div class="xl:col-span-4 bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-slate-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-base font-semibold text-slate-800">
                            Aktivitas Terbaru
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            Aktivitas terakhir di dalam sistem
                        </p>
                    </div>

                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600
                                flex items-center justify-center">

                        <i class="bi bi-clock-history"></i>

                    </div>

                </div>

            </div>


            {{-- Daftar Aktivitas --}}
            <div class="divide-y divide-slate-100">

                @forelse ($logs as $log)

                    <div class="px-5 py-3.5 flex items-start gap-3
                                hover:bg-slate-50 transition">

                        {{-- Icon --}}
                        <div class="w-9 h-9 shrink-0 rounded-full
                                    bg-emerald-50 text-emerald-600
                                    flex items-center justify-center">

                            <i class="bi bi-person-check-fill text-sm"></i>

                        </div>


                        {{-- Informasi --}}
                        <div class="min-w-0 flex-1">

                            <p class="text-xs text-slate-700 leading-relaxed">

                                <span class="font-semibold text-slate-800">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </span>

                                {{ $log->aktivitas }}

                            </p>

                            <p class="text-[11px] text-slate-400 mt-1">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </p>

                        </div>

                    </div>

                @empty

                    <div class="px-5 py-10 text-center">

                        <i class="bi bi-inbox text-3xl text-slate-300"></i>

                        <p class="mt-2 text-sm text-slate-400">
                            Belum ada aktivitas terbaru.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>


        {{-- =================================================
            KONDISI ALAT
        ================================================== --}}
        <div class="xl:col-span-3 bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-slate-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-base font-semibold text-slate-800">
                            Kondisi Alat
                        </h2>

                        <p class="text-xs text-slate-400 mt-1">
                            Ringkasan inventaris
                        </p>
                    </div>

                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600
                                flex items-center justify-center">

                        <i class="bi bi-boxes"></i>

                    </div>

                </div>

            </div>


            {{-- Isi --}}
            <div class="p-5 space-y-5">

                {{-- Total Alat --}}
                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600
                                flex items-center justify-center">

                        <i class="bi bi-box-seam text-lg"></i>

                    </div>

                    <div class="flex-1">

                        <p class="text-xs text-slate-500">
                            Total Alat
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ $totalAlat }}
                        </p>

                    </div>

                </div>


                {{-- Stok Tersedia --}}
                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600
                                flex items-center justify-center">

                        <i class="bi bi-box2-fill text-lg"></i>

                    </div>

                    <div class="flex-1">

                        <p class="text-xs text-slate-500">
                            Stok Tersedia
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ $totalStokTersedia }}
                        </p>

                    </div>

                </div>


                {{-- Alat Rusak --}}
                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-500
                                flex items-center justify-center">

                        <i class="bi bi-tools text-lg"></i>

                    </div>

                    <div class="flex-1">

                        <p class="text-xs text-slate-500">
                            Stok Rusak
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ $totalAlatRusak }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        RINGKASAN TAMBAHAN
    ====================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


        {{-- Total Alat Rusak --}}
        <div class="bg-white border border-rose-100 rounded-xl shadow-sm
                    p-5 hover:shadow-md transition">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500
                            flex items-center justify-center">

                    <i class="bi bi-tools text-xl"></i>

                </div>

                <div>

                    <p class="text-sm text-slate-500">
                        Total Stok Rusak
                    </p>

                    <p class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $totalAlatRusak }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        dari total stok
                    </p>

                </div>

            </div>

        </div>


        {{-- Stok Tersedia --}}
        <div class="bg-white border border-emerald-100 rounded-xl shadow-sm
                    p-5 hover:shadow-md transition">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600
                            flex items-center justify-center">

                    <i class="bi bi-box-seam-fill text-xl"></i>

                </div>

                <div>

                    <p class="text-sm text-slate-500">
                        Stok Tersedia
                    </p>

                    <p class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $totalStokTersedia }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        dari total stok
                    </p>

                </div>

            </div>

        </div>


        {{-- Total Denda --}}
        <div class="bg-white border border-amber-100 rounded-xl shadow-sm
                    p-5 hover:shadow-md transition">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-500
                            flex items-center justify-center">

                    <i class="bi bi-cash-coin text-xl"></i>

                </div>

                <div>

                    <p class="text-sm text-slate-500">
                        Total Denda
                    </p>

                    <p class="text-2xl font-bold text-slate-800 mt-1">
                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        total denda tercatat
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection