@extends('layouts.app')

@section('title', 'Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman & Pengembalian')

@section('content')

    {{-- FILTER LAPORAN --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4 sm:p-5">

        <h3 class="text-lg font-bold text-gray-800">
            Filter Laporan
        </h3>

        <p class="text-sm text-gray-500 mt-1 mb-4">
            Pilih rentang tanggal untuk menampilkan data laporan.
        </p>


        <form action="{{ route('petugas.laporan.index') }}" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

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
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">

                    <button
                        type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700
                            text-white px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            whitespace-nowrap">
                        Tampilkan
                    </button>

                    <a
                        href="{{ route('petugas.laporan.index') }}"
                        class="w-full bg-gray-200 hover:bg-gray-300
                            text-gray-700 px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            text-center whitespace-nowrap">
                        Reset
                    </a>

                    <a
                        href="{{ route('petugas.laporan.cetak', [
                            'dari' => $dari,
                            'sampai' => $sampai
                        ]) }}"
                        target="_blank"
                        class="w-full bg-blue-600 hover:bg-blue-700
                            text-white px-4 py-2 rounded-lg
                            text-sm font-semibold transition
                            text-center whitespace-nowrap">
                        Cetak Laporan
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Pesan sukses --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200
                    text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- Pesan error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200
                    text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


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