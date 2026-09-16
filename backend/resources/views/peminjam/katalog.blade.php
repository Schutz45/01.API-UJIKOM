@extends('layouts.peminjam')

@section('title', 'Daftar Alat - SIPPSD')
@section('header-title', 'Daftar Alat')

@section('content')

    {{-- =====================================================
         ALERT
    ====================================================== --}}
    @if(session('success'))

        <div
            class="mb-5 p-4 rounded-xl
                   bg-emerald-50 border border-emerald-200
                   text-emerald-700">

            <div class="flex items-center gap-2">

                <i class="bi bi-check-circle-fill"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div
            class="mb-5 p-4 rounded-xl
                   bg-red-50 border border-red-200
                   text-red-700">

            <div class="flex items-center gap-2">

                <i class="bi bi-exclamation-circle-fill"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        </div>

    @endif


    {{-- =====================================================
        HERO KATALOG
    ====================================================== --}}
    <section
        class="relative overflow-hidden
            rounded-3xl
            min-h-[320px] md:min-h-[380px]
            mb-8
            border border-slate-200
            shadow-md">


        {{-- =================================================
            BACKGROUND MEGUMIN
        ================================================== --}}
        <img
            src="{{ asset('storage/images/megumin-banner.png') }}"
            alt="Megumin"
            class="absolute inset-0
                w-full h-full
                object-cover">


        {{-- =================================================
            OVERLAY GRADIENT
        ================================================== --}}
        <div
            class="absolute inset-0
                bg-gradient-to-r
                from-white/20
                via-transparent
                to-transparent">
        </div>


        {{-- =================================================
            KONTEN HERO
        ================================================== --}}
        <div
            class="relative z-10
                min-h-[320px] md:min-h-[380px]
                flex items-center">

            <div
                class="w-full
                    px-6
                    md:px-10">


                {{-- =================================================
                    PANEL TEKS
                ================================================== --}}
                <div
                    class="w-full
                        max-w-xl
                        p-6 md:p-7
                        rounded-3xl
                        bg-white/30
                        border-4
                        border-black/50
                        shadow-xl
                        shadow-slate-900/50">


                    {{-- Badge SIPPSD --}}
                    <div class="mb-3">

                        <span
                            class="inline-flex
                                items-center
                                gap-2
                                px-3 py-1
                                rounded-full
                                bg-indigo-300
                                text-indigo-900
                                text-xs
                                font-bold">

                            <span
                                class="w-1.5 h-1.5
                                    rounded-full
                                    bg-indigo-600">
                            </span>

                            SIPPSD

                        </span>

                    </div>


                    {{-- Judul --}}
                    <h1
                        class="text-3xl
                            md:text-4xl
                            font-extrabold
                            tracking-tight
                            text-slate-900">

                        Daftar Alat

                    </h1>


                    {{-- Deskripsi --}}
                    <p
                        class="mt-3
                            text-sm
                            md:text-base
                            text-slate-900
                            font-bold
                            leading-relaxed
                            max-w-lg">

                        Temukan berbagai peralatan sekolah yang tersedia
                        untuk mendukung kegiatan belajar dan kreativitasmu.

                    </p>


                    {{-- =================================================
                        SEARCH
                    ================================================== --}}
                    <div class="mt-5">

                        <div class="relative">

                            <i
                                class="bi bi-search
                                    absolute left-4 top-1/2
                                    -translate-y-1/2
                                    text-slate-400">
                            </i>


                            <input
                                id="searchAlat"
                                type="text"
                                placeholder="Cari alat, kategori, atau nama alat..."
                                class="w-full
                                    pl-11 pr-4 py-3
                                    rounded-xl
                                    border border-slate-900
                                    bg-white
                                    text-sm
                                    text-slate-700
                                    placeholder:text-slate-400
                                    shadow-sm
                                    outline-none
                                    focus:ring-2
                                    focus:ring-indigo-500
                                    focus:border-indigo-500
                                    transition">

                        </div>

                    </div>


                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         JUDUL DAFTAR
    ====================================================== --}}
    <div class="mb-6">

        <h2
            class="text-2xl font-bold
                   text-slate-800">

            Daftar Alat

        </h2>

        <p class="mt-1 text-sm text-slate-500">

            Temukan peralatan sekolah yang sesuai
            dengan kebutuhanmu.

        </p>

    </div>


    {{-- =====================================================
         FORM PEMINJAMAN
    ====================================================== --}}
    <form
        action="{{ route('peminjam.peminjaman.ajukan') }}"
        method="POST">

        @csrf


        {{-- =================================================
             TANGGAL PENGEMBALIAN
        ================================================== --}}
        <div
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   shadow-sm
                   p-5 mb-6">

            <div
                class="flex flex-col
                       md:flex-row
                       md:items-center
                       md:justify-between
                       gap-4">

                <div>

                    <h3
                        class="font-semibold
                               text-slate-800">

                        Rencana Pengembalian

                    </h3>

                    <p
                        class="text-sm
                               text-slate-500
                               mt-1">

                        Tentukan tanggal ketika seluruh
                        alat akan dikembalikan.

                    </p>

                </div>


                <div class="w-full md:w-72">

                    <input
                        type="date"
                        name="tgl_kembali_plan"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        required
                        class="w-full
                               px-4 py-2.5
                               rounded-xl
                               border border-slate-300
                               focus:ring-2
                               focus:ring-indigo-500
                               focus:border-indigo-500
                               outline-none
                               transition">

                </div>

            </div>

        </div>


        {{-- =================================================
             DAFTAR ALAT
        ================================================== --}}
        @if($alats->count() > 0)

            <div
                id="daftarAlat"
                class="grid
                       grid-cols-1
                       sm:grid-cols-2
                       xl:grid-cols-3
                       gap-5">


                @foreach($alats as $alat)

                    <div
                        class="alat-card
                               bg-white
                               rounded-2xl
                               border border-slate-200
                               shadow-sm
                               overflow-hidden
                               hover:shadow-md
                               hover:-translate-y-1
                               transition duration-200"
                        data-search="
                            {{ strtolower(
                                $alat->nama_alat . ' ' .
                                ($alat->kategori->nama_kategori ?? '')
                            ) }}">

                        {{-- =============================
                             GAMBAR ALAT
                        ============================== --}}
                        <div
                            class="h-48
                                   bg-slate-100
                                   flex items-center
                                   justify-center
                                   overflow-hidden">

                            @if($alat->gambar)

                                <img
                                    src="{{ asset($alat->gambar) }}"
                                    alt="{{ $alat->nama_alat }}"
                                    class="w-full h-full
                                           object-contain
                                           p-4
                                           transition duration-300
                                           hover:scale-105">

                            @else

                                <div
                                    class="flex flex-col
                                           items-center
                                           text-slate-400">

                                    <i
                                        class="bi bi-image
                                               text-5xl">
                                    </i>

                                    <span
                                        class="text-xs mt-2">

                                        Tidak ada gambar

                                    </span>

                                </div>

                            @endif

                        </div>


                        {{-- =============================
                             ISI CARD
                        ============================== --}}
                        <div class="p-5">

                            {{-- Nama --}}
                            <h3
                                class="font-bold text-lg
                                       text-slate-800
                                       truncate"
                                title="{{ $alat->nama_alat }}">

                                {{ $alat->nama_alat }}

                            </h3>


                            {{-- Kategori --}}
                            <p
                                class="text-sm
                                       text-slate-500
                                       mt-1">

                                {{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}

                            </p>


                            {{-- Status + Stok --}}
                            <div
                                class="flex items-center
                                       justify-between
                                       mt-4">

                                <span
                                    class="inline-flex
                                           items-center gap-1.5
                                           px-2.5 py-1
                                           rounded-full
                                           bg-emerald-100
                                           text-emerald-700
                                           text-xs font-semibold">

                                    <span
                                        class="w-1.5 h-1.5
                                               rounded-full
                                               bg-emerald-500">
                                    </span>

                                    Tersedia

                                </span>


                                <span
                                    class="text-sm
                                           text-slate-500">

                                    Stok:

                                    <strong
                                        class="text-slate-700">

                                        {{ $alat->stok }}

                                    </strong>

                                </span>

                            </div>


                            {{-- =========================
                                 PILIH + JUMLAH
                            ========================== --}}
                            <div
                                class="mt-5 pt-4
                                       border-t
                                       border-slate-100">

                                <div
                                    class="flex flex-col sm:flex-row
                                            sm:items-center
                                            sm:justify-between
                                            gap-3">

                                    <label
                                        class="flex items-center
                                               gap-2
                                               cursor-pointer">

                                        <input
                                            type="checkbox"
                                            name="alat_id[]"
                                            value="{{ $alat->id }}"
                                            class="w-4 h-4
                                                   text-indigo-600
                                                   border-slate-300
                                                   rounded
                                                   focus:ring-indigo-500">

                                        <span
                                            class="text-sm
                                                   font-medium
                                                   text-slate-600">

                                            Pilih alat

                                        </span>

                                    </label>


                                    <input
                                        type="number"
                                        name="jumlah[{{ $alat->id }}]"
                                        value="1"
                                        min="1"
                                        max="{{ $alat->stok }}"
                                        class="w-20
                                               px-3 py-2
                                               rounded-lg
                                               border
                                               border-slate-300
                                               text-sm
                                               text-center
                                               focus:ring-2
                                               focus:ring-indigo-500
                                               focus:border-indigo-500
                                               outline-none">

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- =================================================
                 HASIL SEARCH KOSONG
            ================================================== --}}
            <div
                id="searchEmpty"
                class="hidden
                       bg-white
                       rounded-2xl
                       border border-slate-200
                       shadow-sm
                       p-12
                       text-center">

                <div
                    class="w-16 h-16
                           mx-auto
                           rounded-full
                           bg-slate-100
                           flex items-center
                           justify-center">

                    <i
                        class="bi bi-search
                               text-3xl
                               text-slate-400">
                    </i>

                </div>

                <h3
                    class="mt-4
                           font-semibold
                           text-slate-700">

                    Alat Tidak Ditemukan

                </h3>

                <p
                    class="mt-1
                           text-sm
                           text-slate-400">

                    Coba gunakan kata pencarian
                    yang berbeda.

                </p>

            </div>


            {{-- =================================================
                 TOMBOL AJUKAN
            ================================================== --}}
            <div
                class="mt-6
                       flex justify-end">

                <button
                    type="submit"
                    class="w-full sm:w-auto
                           inline-flex
                           items-center justify-center 
                           gap-2
                           px-6 py-3
                           rounded-xl
                           bg-indigo-600
                           text-white
                           font-semibold
                           text-sm
                           hover:bg-indigo-700
                           focus:ring-4
                           focus:ring-indigo-100
                           transition
                           shadow-sm">

                    <i class="bi bi-send-fill"></i>

                    Ajukan Peminjaman

                </button>

            </div>


        @else

            {{-- =================================================
                 EMPTY STATE
            ================================================== --}}
            <div
                class="bg-white
                       rounded-2xl
                       border border-slate-200
                       shadow-sm
                       p-12
                       text-center">

                <div
                    class="w-16 h-16
                           mx-auto
                           rounded-full
                           bg-slate-100
                           flex items-center
                           justify-center">

                    <i
                        class="bi bi-inbox
                               text-3xl
                               text-slate-400">
                    </i>

                </div>

                <h3
                    class="mt-4
                           font-semibold
                           text-slate-700">

                    Tidak Ada Alat Tersedia

                </h3>

                <p
                    class="mt-1
                           text-sm
                           text-slate-400">

                    Saat ini belum ada peralatan
                    yang dapat dipinjam.

                </p>

            </div>

        @endif

    </form>

@endsection


{{-- =========================================================
     SEARCH SCRIPT
========================================================= --}}
@push('scripts')

<script>

    const searchInput = document.getElementById('searchAlat');
    const alatCards = document.querySelectorAll('.alat-card');
    const searchEmpty = document.getElementById('searchEmpty');

    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            let jumlahTampil = 0;

            alatCards.forEach(function (card) {

                const data = card.dataset.search.toLowerCase();

                if (data.includes(keyword)) {

                    card.classList.remove('hidden');
                    jumlahTampil++;

                } else {

                    card.classList.add('hidden');

                }

            });


            if (searchEmpty) {

                if (jumlahTampil === 0) {

                    searchEmpty.classList.remove('hidden');

                } else {

                    searchEmpty.classList.add('hidden');

                }

            }

        });

    }

</script>

@endpush