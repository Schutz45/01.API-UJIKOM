@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Dashboard Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800
                    p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-800 text-red-800
                    p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- Container --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50
                    flex flex-col lg:flex-row
                    lg:items-center lg:justify-between
                    gap-4">

            {{-- Judul --}}
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-800">
                    Menunggu Verifikasi Persetujuan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar pengajuan peminjaman yang menunggu persetujuan petugas.
                </p>
            </div>


            {{-- Form Search --}}
            <form
                action="{{ route('petugas.peminjaman.index') }}"
                method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto"
            >

                <div class="flex w-full sm:w-80">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peminjam..."
                        class="w-full px-3 py-2 text-sm border border-gray-300
                               rounded-lg sm:rounded-r-none
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <button
                        type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white
                               px-4 py-2 text-sm font-semibold
                               rounded-lg sm:rounded-l-none
                               transition whitespace-nowrap"
                    >
                        Cari
                    </button>
                </div>

                @if(request('search'))
                    <a
                        href="{{ route('petugas.peminjaman.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700
                               px-3 py-2 text-sm rounded-lg
                               flex items-center justify-center
                               transition whitespace-nowrap"
                    >
                        Reset
                    </a>
                @endif

            </form>

        </div>


        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[950px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Rencana Kembali
                        </th>

                        <th class="py-3 px-4 border-b min-w-[280px]">
                            Detail Alat
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[170px]">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjamans as $item)

                        <tr class="hover:bg-gray-50 transition align-top">

                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->user->name ?? '-' }}
                            </td>


                            {{-- Tanggal Pinjam --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_pinjam }}
                            </td>


                            {{-- Rencana Kembali --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_kembali_plan }}
                            </td>


                            {{-- Detail Alat --}}
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


                            {{-- Aksi --}}
                            <td class="py-3 px-4 border-b text-center min-w-[170px]">

                                @if($item->status == 'diajukan')

                                    <div class="flex justify-center items-center gap-2 whitespace-nowrap">

                                        {{-- Tombol Setujui --}}
                                        <form
                                            action="{{ route('petugas.peminjaman.setujui', $item->id) }}"
                                            method="POST"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Setujui peminjaman alat ini?')"
                                                class="bg-emerald-600 hover:bg-emerald-700
                                                       text-white px-3 py-1.5 rounded
                                                       text-xs font-semibold
                                                       transition shadow-sm"
                                            >
                                                Setujui
                                            </button>
                                        </form>


                                        {{-- Tombol Tolak --}}
                                        <form
                                            action="{{ route('petugas.peminjaman.tolak', $item->id) }}"
                                            method="POST"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Yakin ingin menolak peminjaman alat ini?')"
                                                class="bg-red-500 hover:bg-red-600
                                                       text-white px-3 py-1.5 rounded
                                                       text-xs font-semibold
                                                       transition shadow-sm"
                                            >
                                                Tolak
                                            </button>
                                        </form>

                                    </div>

                                @else

                                    <span class="text-xs font-semibold text-blue-600
                                                 bg-blue-50 px-2.5 py-1 rounded">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="py-6 text-center text-gray-500"
                            >
                                Tidak ada pengajuan peminjaman baru.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection