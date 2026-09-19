@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')
    
@section('content')
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-b-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                {{-- Judul --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Transaksi Peminjaman
                    </h3>
                </div>

                {{-- Kontrol --}}
                <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">

                    {{-- Filter + Search --}}
                    <form action="{{ route('admin.peminjaman.index') }}"
                        method="GET"
                        class="flex flex-col sm:flex-row sm:flex-wrap gap-2 w-full xl:w-auto">

                        {{-- Filter Status --}}
                        <select name="status"
                            class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg
                            bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="">Semua Status</option>

                            <option value="diajukan"
                                {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                                Diajukan
                            </option>

                            <option value="dipinjam"
                                {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                                Dipinjam
                            </option>

                            <option value="dikembalikan"
                                {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                Dikembalikan
                            </option>

                            <option value="telat"
                                {{ request('status') == 'telat' ? 'selected' : '' }}>
                                Telat
                            </option>
                        </select>

                        {{-- Filter Urutan --}}
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
                                placeholder="Cari nama peminjam / status..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                                focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2
                                text-sm font-semibold rounded-r-lg transition whitespace-nowrap">
                                Cari
                            </button>
                        </div>

                        {{-- Reset --}}
                        @if (request('search') || request('status') || request('sort'))
                            <a href="{{ route('admin.peminjaman.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2
                                text-sm rounded-lg flex items-center justify-center transition whitespace-nowrap">
                                Reset
                            </a>
                        @endif

                    </form>

                    {{-- Tambah --}}
                    <a href="{{ route('admin.peminjaman.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                        px-4 py-2 rounded-lg transition whitespace-nowrap text-center
                        flex items-center justify-center">
                        + Tambah Peminjaman
                    </a>

                </div>

            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[950px] w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b whitespace-nowrap">Peminjam</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Alat yang Dipinjam</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Tgl Pinjam / Rencana Kembali</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($peminjamans as $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                                {{ $peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b min-w-[250px]">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($peminjaman->detailPinjam as $detail)
                                        <li class="whitespace-nowrap">
                                            <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">({{ $detail->jumlah }} pcs)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-xs text-gray-500 whitespace-nowrap">
                                <span class="block">Pinjam: {{ $peminjaman->tgl_pinjam }}</span>
                                <span class="block font-semibold">Rencana: {{ $peminjaman->tgl_kembali_plan }}</span>
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                    @elseif($peminjaman->status == 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($peminjaman->status == 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b min-w-[170px]">
                                <div class="flex flex-col gap-2">

                                    <!-- Tombol Kembalikan -->
                                    @if (in_array($peminjaman->status, ['dipinjam', 'telat']))
                                        <a href="{{ route('admin.pengembalian.create', $peminjaman->id) }}"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition text-center whitespace-nowrap">
                                            Kembalikan
                                        </a>
                                    @endif

                                    <!-- Form Ubah Status Cepat -->
                                    <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST" class="flex items-center">
                                        @csrf 
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none">
                                            <option value="diajukan"        {{ $peminjaman->status == 'diajukan' ? 'selected' : '' }}   >Diajukan</option>
                                            <option value="dipinjam"        {{ $peminjaman->status == 'dipinjam' ? 'selected' : '' }}   >Dipinjam</option>
                                            <option value="telat"           {{ $peminjaman->status == 'telat' ? 'selected' : '' }}      >Telat</option>
                                        </select>
                                    </form>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition w-full whitespace-nowrap">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $peminjamans->links() }}
        </div>
    </div>
@endsection