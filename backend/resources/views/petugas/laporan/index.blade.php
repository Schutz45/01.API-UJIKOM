@extends('layouts.app')

@section('title', 'Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman & Pengembalian')

@push('styles')
<style>
    @media print {
        /* Sembunyikan elemen UI yang tidak perlu dicetak */
        nav, aside, header, .sidebar, .navbar, 
        .no-print, #notificationDropdown, #userDropdown,
        form, .filter-section, .alert, button, a.btn, 
        .tombol-cetak-container {
            display: none !important;
        }

        /* Reset padding/margin untuk area cetak */
        body, .content-wrapper, main {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .bg-white {
            border: none !important;
            box-shadow: none !important;
        }

        /* Pastikan tabel terlihat bagus saat dicetak */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #e2e8f0 !important;
            padding: 8px !important;
            font-size: 10pt !important;
        }
        
        /* Tampilkan judul laporan khusus cetak jika perlu */
        .print-only {
            display: block !important;
        }
    }
    .print-only { display: none; }
</style>
@endpush

@section('content')

    {{-- JUDUL CETAK (Hanya Muncul saat Print) --}}
    <div class="print-only mb-6 text-center">
        <h1 class="text-2xl font-bold uppercase tracking-widest">SIPPSD</h1>
        <h2 class="text-lg font-semibold">Laporan Peminjaman & Pengembalian Alat</h2>
        <p class="text-sm text-gray-600 mt-1">
            Periode: {{ $dari ? date('d/m/Y', strtotime($dari)) : 'Semua' }} 
            s/d {{ $sampai ? date('d/m/Y', strtotime($sampai)) : 'Semua' }}
        </p>
        <hr class="mt-4 border-gray-300">
    </div>

    {{-- FILTER LAPORAN --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4 sm:p-5 no-print filter-section">

        <h3 class="text-lg font-bold text-gray-800">
            Filter Laporan
        </h3>

        <p class="text-sm text-gray-500 mt-1 mb-4">
            Pilih rentang tanggal untuk menampilkan data laporan.
        </p>


        <form action="{{ route('petugas.laporan.index') }}" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                {{-- Dari Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Dari Tanggal
                    </label>

                    <input
                        type="date"
                        name="dari"
                        value="{{ $dari ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>


                {{-- Sampai Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sampai Tanggal
                    </label>

                    <input
                        type="date"
                        name="sampai"
                        value="{{ $sampai ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                </div>


                {{-- Tombol --}}
                <div class="flex flex-col sm:flex-row sm:justify-end gap-2 w-full md:col-span-2 tombol-cetak-container">

                    <button
                        type="submit"
                        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700
                            text-white px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            whitespace-nowrap">
                        Tampilkan
                    </button>

                    <a
                        href="{{ route('petugas.laporan.index') }}"
                        class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300
                            text-gray-700 px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            text-center whitespace-nowrap">
                        Reset
                    </a>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700
                            text-white px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            text-center whitespace-nowrap">
                        Cetak Laporan
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- ========================================= --}}
    {{-- LAPORAN PEMINJAMAN --}}
    {{-- ========================================= --}}

    <div class="bg-white rounded-lg shadow-sm overflow-hidden
                border border-gray-200 mb-6">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Laporan Peminjaman
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Daftar seluruh data peminjaman alat.
            </p>

        </div>


        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[950px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            No
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Batas Kembali
                        </th>

                        <th class="py-3 px-4 border-b min-w-[280px]">
                            Alat
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap">
                            Status
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjamans as $item)

                        <tr class="hover:bg-gray-50 transition align-top">

                            {{-- No --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $loop->iteration }}
                            </td>


                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b font-medium
                                       text-gray-900 whitespace-nowrap">
                                {{ $item->user->name ?? '-' }}
                            </td>


                            {{-- Tanggal Pinjam --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_pinjam }}
                            </td>


                            {{-- Batas Kembali --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_kembali_plan }}
                            </td>


                            {{-- Alat --}}
                            <td class="py-3 px-4 border-b min-w-[280px]">

                                <ul class="list-disc list-inside space-y-1 text-xs">

                                    @foreach($item->detailPinjam as $detail)

                                        <li class="whitespace-nowrap">

                                            <span class="font-semibold">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>

                                            (Jumlah: {{ $detail->jumlah }})

                                        </li>

                                    @endforeach

                                </ul>

                            </td>


                            {{-- Status --}}
                            <td class="py-3 px-4 border-b text-center whitespace-nowrap">

                                @if($item->status === 'diajukan')

                                    <span class="inline-block text-xs font-semibold
                                                 text-yellow-600 bg-yellow-50
                                                 px-2.5 py-1 rounded">
                                        Diajukan
                                    </span>

                                @elseif($item->status === 'dipinjam')

                                    <span class="inline-block text-xs font-semibold
                                                 text-blue-600 bg-blue-50
                                                 px-2.5 py-1 rounded">
                                        Dipinjam
                                    </span>

                                @elseif($item->status === 'telat')

                                    <span class="inline-block text-xs font-semibold
                                                 text-red-600 bg-red-50
                                                 px-2.5 py-1 rounded">
                                        Telat
                                    </span>

                                @elseif($item->status === 'dikembalikan')

                                    <span class="inline-block text-xs font-semibold
                                                 text-emerald-600 bg-emerald-50
                                                 px-2.5 py-1 rounded">
                                        Dikembalikan
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-6 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ========================================= --}}
    {{-- LAPORAN PENGEMBALIAN --}}
    {{-- ========================================= --}}

    <div class="bg-white rounded-lg shadow-sm overflow-hidden
                border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Laporan Pengembalian
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Daftar seluruh data pengembalian dan denda.
            </p>

        </div>


        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[1250px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            No
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Tanggal Kembali
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Kondisi
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Denda Keterlambatan
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Denda Kerusakan
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Total Denda
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Petugas
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($pengembalians as $item)

                        <tr class="hover:bg-gray-50 transition align-top">

                            {{-- No --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $loop->iteration }}
                            </td>


                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b font-medium
                                       text-gray-900 whitespace-nowrap">
                                {{ $item->peminjaman->user->name ?? '-' }}
                            </td>


                            {{-- Tanggal Pinjam --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->peminjaman->tgl_pinjam ?? '-' }}
                            </td>


                            {{-- Tanggal Kembali --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_kembali ?? '-' }}
                            </td>


                            {{-- Kondisi --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">

                                @if($item->kondisi_kembali === 'baik')

                                    <span class="inline-block text-xs font-semibold
                                                 text-emerald-600 bg-emerald-50
                                                 px-2.5 py-1 rounded">
                                        Baik
                                    </span>

                                @elseif($item->kondisi_kembali === 'rusak')

                                    <span class="inline-block text-xs font-semibold
                                                 text-red-600 bg-red-50
                                                 px-2.5 py-1 rounded">
                                        Rusak
                                    </span>

                                @else
                                    -
                                @endif

                            </td>


                            {{-- Denda Keterlambatan --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                Rp {{ number_format($item->denda_keterlambatan ?? 0, 0, ',', '.') }}
                            </td>


                            {{-- Denda Kerusakan --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                Rp {{ number_format($item->denda_kerusakan ?? 0, 0, ',', '.') }}
                            </td>


                            {{-- Total Denda --}}
                            <td class="py-3 px-4 border-b font-semibold whitespace-nowrap">
                                Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                            </td>


                            {{-- Petugas --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->petugas->name ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="py-6 text-center text-gray-500"
                            >
                                Belum ada data pengembalian.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection