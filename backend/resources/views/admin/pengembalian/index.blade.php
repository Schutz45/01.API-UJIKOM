@extends('layouts.app')

@section('title', 'Riwayat Pengembalian - Panel Admin')
@section('header-title', 'Riwayat Pengembalian Alat')

@section('content')

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">

        <h3 class="text-lg font-bold text-gray-800">
            Riwayat Pengembalian
        </h3>

        {{-- Search --}}
        <form
            action="{{ route('admin.pengembalian.index') }}"
            method="GET"
            class="flex w-full sm:w-80"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama peminjam / kondisi..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            <button
                type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition whitespace-nowrap"
            >
                Cari
            </button>

            @if(request('search'))
                <a
                    href="{{ route('admin.pengembalian.index') }}"
                    class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition whitespace-nowrap"
                >
                    Reset
                </a>
            @endif
        </form>

    </div>

    {{-- Pesan sukses --}}
    @if(session('success'))
        <div class="m-5 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pesan error --}}
    @if(session('error'))
        <div class="m-5 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="w-full overflow-x-auto">

        <table class="min-w-[1100px] w-full text-left border-collapse">

            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Peminjam
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Alat
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Rencana Kembali
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Tanggal Kembali
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Kondisi
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Denda
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Petugas
                    </th>

                    <th class="py-3 px-4 border-b text-center whitespace-nowrap">
                        Aksi 
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($pengembalians as $pengembalian)

                    <tr class="hover:bg-gray-50 transition align-top">

                        {{-- Peminjam --}}
                        <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                            {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
                        </td>

                        {{-- Alat --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            <ul class="list-disc list-inside space-y-1">

                                @foreach($pengembalian->peminjaman->detailPinjam as $detail)

                                    <li class="whitespace-nowrap">
                                        <span class="font-semibold">
                                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                        </span>

                                        <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">
                                            ({{ $detail->jumlah }} pcs)
                                        </span>
                                    </li>

                                @endforeach

                            </ul>

                        </td>

                        {{-- Rencana kembali --}}
                        <td class="py-3 px-4 border-b text-xs text-gray-500">
                            {{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d-m-Y') }}
                        </td>

                        {{-- Tanggal kembali --}}
                        <td class="py-3 px-4 border-b text-xs whitespace-nowrap">
                            {{ $pengembalian->tgl_kembali?->format('d-m-Y') }}
                        </td>

                        {{-- Kondisi --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            @if($pengembalian->kondisi_kembali === 'baik')

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    Baik
                                </span>

                            @elseif($pengembalian->kondisi_kembali === 'rusak')

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rusak
                                </span>

                            @else

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($pengembalian->kondisi_kembali) }}
                                </span>

                            @endif

                        </td>

                        {{-- Denda --}}
                        <td class="py-3 px-4 border-b font-semibold whitespace-nowrap">
                            Rp{{ number_format($pengembalian->denda, 0, ',', '.') }}
                        </td>

                        {{-- Petugas --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            {{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}
                        </td>

                        {{-- Aksi --}} 
                        <td class="py-3 px-4 border-b text-center min-w-[160px]">
                            <div class="flex justify-center items-center gap-2 whitespace-nowrap">

                            {{-- Edit --}}
                                <a href="{{ route('admin.pengembalian.edit', $pengembalian->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition" >
                                    Edit
                                </a> 

                            {{-- Hapus --}} 
                                <form action="{{ route('admin.pengembalian.destroy', $pengembalian->id) }}" 
                                    method="POST" onsubmit="return confirm('Yakin ingin menghapus data pengembalian ini? Stok alat akan dikurangi kembali dan status peminjaman menjadi dipinjam.');" > @csrf @method('DELETE') 
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition" >
                                         Hapus 
                                    </button> 
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="py-8 text-center text-gray-500"
                        >
                            Belum ada riwayat pengembalian.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $pengembalians->links() }}
    </div>

</div>

@endsection