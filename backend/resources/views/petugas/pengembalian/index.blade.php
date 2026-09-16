@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan Pengembalian Alat')

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
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800
                    p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- Container --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Peminjaman Aktif
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Daftar alat yang masih dipinjam atau sudah terlambat dikembalikan.
            </p>

        </div>


        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[1100px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

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
                            Detail Alat
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[190px]">
                            Status
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[190px]">
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


                            {{-- Batas Kembali --}}
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


                            {{-- Status --}}
                            <td class="py-3 px-4 border-b text-center whitespace-nowrap">

                                @if($item->status === 'dipinjam')

                                    @if($item->permintaan_pengembalian)

                                        <span class="inline-block text-xs font-semibold
                                                     text-amber-600 bg-amber-50
                                                     px-2.5 py-1 rounded">
                                            Menunggu Pengembalian
                                        </span>

                                    @else

                                        <span class="inline-block text-xs font-semibold
                                                     text-blue-600 bg-blue-50
                                                     px-2.5 py-1 rounded">
                                            Dipinjam
                                        </span>

                                    @endif

                                @elseif($item->status === 'telat')

                                    @if($item->permintaan_pengembalian)

                                        <span class="inline-block text-xs font-semibold
                                                     text-amber-600 bg-amber-50
                                                     px-2.5 py-1 rounded">
                                            Menunggu Pengembalian
                                        </span>

                                    @else

                                        <span class="inline-block text-xs font-semibold
                                                     text-red-600 bg-red-50
                                                     px-2.5 py-1 rounded">
                                            Telat
                                        </span>

                                    @endif

                                @endif

                            </td>


                            {{-- Aksi --}}
                            <td class="py-3 px-4 border-b text-center min-w-[190px]">

                                <div class="flex justify-center items-center whitespace-nowrap">

                                    @if($item->permintaan_pengembalian)

                                        <a
                                            href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600
                                                   text-white px-3 py-1.5 rounded
                                                   text-xs font-semibold
                                                   transition shadow-sm"
                                        >
                                            Menunggu Pengembalian
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                            class="bg-emerald-600 hover:bg-emerald-700
                                                   text-white px-3 py-1.5 rounded
                                                   text-xs font-semibold
                                                   transition shadow-sm"
                                        >
                                            Proses Pengembalian
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-6 text-center text-gray-500"
                            >
                                Tidak ada peminjaman yang sedang aktif.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection