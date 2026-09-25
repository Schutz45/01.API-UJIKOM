@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admi')
@section('header-title', 'Manajemen Data Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                {{-- Judul --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Alat
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Menampilkan daftar seluruh unit alat.
                    </p>
                </div>

                {{-- Kontrol --}}
                <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">

                    {{-- Filter + Search --}}
                    <form action="{{ route('admin.alat.index') }}"
                        method="GET"
                        class="flex flex-col sm:flex-row sm:flex-wrap gap-2 w-full xl:w-auto">

                        {{-- Filter --}}
                        <select name="sort"
                            class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg
                            bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="terbaru"
                                {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                                Terbaru
                            </option>

                            <option value="terlama"
                                {{ request('sort') == 'terlama' ? 'selected' : '' }}>
                                Terlama
                            </option>

                            <option value="az"
                                {{ request('sort') == 'az' ? 'selected' : '' }}>
                                A → Z
                            </option>

                            <option value="za"
                                {{ request('sort') == 'za' ? 'selected' : '' }}>
                                Z → A
                            </option>
                        </select>

                        {{-- Search --}}
                        <div class="flex w-full sm:w-80">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama alat / kategori..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                                focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2
                                text-sm font-semibold rounded-r-lg transition whitespace-nowrap">
                                Cari
                            </button>
                        </div>

                        {{-- Reset --}}
                        @if (request('search') || request('sort'))
                            <a href="{{ route('admin.alat.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2
                                text-sm rounded-lg flex items-center justify-center transition whitespace-nowrap">
                                Reset
                            </a>
                        @endif

                    </form>

                    {{-- Tambah --}}
                    <a href="{{ route('admin.alat.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                        px-4 py-2 rounded-lg transition whitespace-nowrap text-center
                        flex items-center justify-center">
                        + Tambah Alat
                    </a>



                </div>

            </div>

        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-[600px] w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Alat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kondisi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($alats as $alat)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="py-3 px-4 border-b">
                            @if($alat->gambar)
                                <img
                                    src="{{ asset($alat->gambar) }}"
                                    alt="{{ $alat->nama_alat }}"
                                    class="w-12 h-12 object-cover rounded-lg border">
                            @else
                                <span class="text-xs text-gray-400 italic">
                                    Tidak Ada
                                </span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                            {{ $alat->nama_alat }}
                        </td>

                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            {{ $alat->kategori->nama_kategori ?? '-' }}
                        </td>

                        <td class="py-3 px-4 border-b font-semibold whitespace-nowrap">
                            {{ $alat->stok }}
                        </td>

                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                @if($alat->status_kondisi == 'baik') bg-green-100 text-green-800
                                @elseif($alat->status_kondisi == 'sebagian_rusak') bg-yellow-100 text-yellow-800
                                @elseif($alat->status_kondisi == 'rusak') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $alat->status_kondisi_label }}
                            </span>
                            @if($alat->stok_rusak > 0)
                                <span class="block text-xs text-red-500 mt-0.5">({{ $alat->stok_rusak }} rusak)</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            <div class="flex items-center space-x-2">

                                <a href="{{ route('admin.alat.edit', $alat->id) }}"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Edit
                                </a>

                                <form action="{{ route('admin.alat.destroy', $alat->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus alat ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Hapus
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                Belum ada data alat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $alats->links() }}
        </div>
    </div>
@endsection