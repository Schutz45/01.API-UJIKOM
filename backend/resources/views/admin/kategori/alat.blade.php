@extends('layouts.app')

@section('title', 'Alat dalam Kategori - Panel Admin')
@section('header-title', 'Alat dalam Kategori')

@section('content')

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

        {{-- Judul --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Alat dalam Kategori: {{ $kategori->nama_kategori }}
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar alat yang termasuk dalam kategori ini.
                </p>
            </div>

            {{-- Kontrol --}}
            <div class="flex flex-col sm:flex-row gap-2 w-full xl:w-auto">

                <form
                    action="{{ route('admin.kategori.alat', $kategori) }}"
                    method="GET"
                    class="w-full sm:w-auto"
                >

                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <select
                        name="kondisi"
                        onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                        <option value=""
                            {{ request('kondisi') == '' ? 'selected' : '' }}>
                            Semua Kondisi
                        </option>

                        <option value="baik"
                            {{ request('kondisi') == 'baik' ? 'selected' : '' }}>
                            Baik
                        </option>

                        <option value="rusak"
                            {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>
                            Rusak
                        </option>

                    </select>

                </form>

                {{-- Search --}}
                <form
                    action="{{ route('admin.kategori.alat', $kategori) }}"
                    method="GET"
                    class="flex w-full sm:w-80"
                >

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama alat / kondisi..."
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
                            href="{{ route('admin.kategori.alat', $kategori) }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center justify-center transition whitespace-nowrap"
                        >
                            Reset
                        </a>
                    @endif

                </form>

                {{-- Kembali --}}
                <a
                    href="{{ route('admin.kategori.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap text-center"
                >
                    ← Kembali
                </a>

            </div>

        </div>

    </div>


    {{-- Tabel --}}
    <div class="overflow-x-auto">

        <table class="min-w-[800px] w-full text-left border-collapse">

            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        No
                    </th>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        Gambar
                    </th>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        Nama Alat
                    </th>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        Stok
                    </th>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        Kondisi
                    </th>

                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">
                        Deskripsi
                    </th>

                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">

                @forelse ($alats as $alat)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $alats->firstItem() + $loop->index }}
                        </td>

                        <td class="py-3 px-4 border-b">
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-12 h-12 object-cover rounded-lg border">
                            @else
                                <span class="text-xs text-gray-400 italic">Tidak Ada</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            {{ $alat->nama_alat }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $alat->stok }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            @if (strtolower($alat->status_kondisi) === 'baik')

                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                    {{ $alat->status_kondisi }}
                                </span>

                            @elseif (strtolower($alat->status_kondisi) === 'rusak')

                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                    {{ $alat->status_kondisi }}
                                </span>

                            @else

                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                    {{ $alat->status_kondisi }}
                                </span>

                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $alat->deskripsi ?: '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center">

                            <div class="text-gray-400 text-3xl mb-2">
                                📦
                            </div>

                            <p class="text-sm font-semibold text-gray-600">
                                Belum ada alat dalam kategori ini.
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Tambahkan alat melalui menu Kelola Alat.
                            </p>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    @if ($alats->hasPages())

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $alats->links() }}
        </div>

    @endif

</div>

@endsection