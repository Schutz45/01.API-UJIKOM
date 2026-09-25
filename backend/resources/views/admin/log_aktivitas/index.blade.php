@extends('layouts.app')

@section('title', 'Log Aktivitas - Panel Admin')
@section('header-title', 'Log Aktivitas')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

        <h3 class="text-lg font-bold text-gray-800">
            Log Aktivitas
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Riwayat aktivitas pengguna dalam sistem.
        </p>

    </div>

    {{-- Form Filter --}}
    <div class="p-4 sm:p-5 border-b border-gray-200">
        <form action="{{ route('admin.log_aktivitas.index') }}" method="GET" class="space-y-4">

            {{-- Search --}}
            <div>
                <div class="flex w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari aktivitas..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                        focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2
                        text-sm font-semibold rounded-r-lg transition whitespace-nowrap">
                        Cari
                    </button>
                </div>
            </div>

            {{-- Filter Dropdown --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- Filter Pengguna --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pengguna</label>
                    <select name="user_id"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Role --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Role</label>
                    <select name="role"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="peminjam" {{ request('role') === 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                    </select>
                </div>

                {{-- Filter Jenis Aktivitas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Aktivitas</label>
                    <select name="jenis"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="auth" {{ request('jenis') === 'auth' ? 'selected' : '' }}>Login / Autentikasi</option>
                        <option value="alat" {{ request('jenis') === 'alat' ? 'selected' : '' }}>Pengelolaan Alat</option>
                        <option value="peminjaman" {{ request('jenis') === 'peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                        <option value="pengembalian" {{ request('jenis') === 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                    </select>
                </div>

                {{-- Filter Tanggal Dari --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                        focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Filter Tanggal Sampai --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                        focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Sort + Action Buttons --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                {{-- Sort --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">Urutkan:</label>
                    <select name="sort"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-2">
                    @if(request()->hasAny(['search', 'user_id', 'jenis', 'role', 'dari', 'sampai', 'sort']) && !(request('sort') === 'terbaru' && count(request()->except('sort')) === 0))
                        <a href="{{ route('admin.log_aktivitas.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2
                            text-sm font-semibold rounded-lg transition whitespace-nowrap">
                            Reset
                        </a>
                    @endif

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2
                        text-sm font-semibold rounded-lg transition whitespace-nowrap">
                        Terapkan Filter
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Tabel --}}
    <div class="w-full overflow-x-auto">

        <table class="min-w-[750px] w-full text-left border-collapse">

            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                    <th class="py-3 px-4 border-b whitespace-nowrap">No</th>
                    <th class="py-3 px-4 border-b whitespace-nowrap">Pengguna</th>
                    <th class="py-3 px-4 border-b whitespace-nowrap">Jenis</th>
                    <th class="py-3 px-4 border-b">Aktivitas</th>
                    <th class="py-3 px-4 border-b whitespace-nowrap">Waktu</th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- No --}}
                        <td class="py-3 px-4 border-b text-gray-500">
                            {{ $loop->iteration + ($logs->firstItem() - 1) }}
                        </td>

                        {{-- Pengguna --}}
                        <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                            <div>{{ $log->user->name ?? 'User Dihapus' }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">{{ $log->user->role ?? '-' }}</div>
                        </td>

                        {{-- Jenis --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @switch($log->jenis)
                                    @case('auth') bg-gray-100 text-gray-700 @break
                                    @case('alat') bg-blue-100 text-blue-700 @break
                                    @case('peminjaman') bg-emerald-100 text-emerald-700 @break
                                    @case('pengembalian') bg-amber-100 text-amber-700 @break
                                    @default bg-gray-100 text-gray-600
                                @endswitch">
                                {{ ucfirst($log->jenis ?? 'umum') }}
                            </span>
                        </td>

                        {{-- Aktivitas --}}
                        <td class="py-3 px-4 border-b">
                            {{ $log->aktivitas }}
                        </td>

                        {{-- Waktu --}}
                        <td class="py-3 px-4 border-b text-xs text-gray-500 whitespace-nowrap">
                            {{ $log->created_at?->format('d-m-Y H:i:s') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Tidak ada aktivitas yang sesuai dengan filter.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">

        <div class="flex flex-col sm:flex-row
                    items-center justify-between
                    gap-3">

            {{-- Informasi Pagination --}}
            <div class="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                Menampilkan
                <span class="font-medium text-gray-700">{{ $logs->firstItem() ?? 0 }}</span>
                -
                <span class="font-medium text-gray-700">{{ $logs->lastItem() ?? 0 }}</span>
                dari
                <span class="font-medium text-gray-700">{{ $logs->total() }}</span>
                aktivitas
            </div>

            {{-- Tombol Pagination --}}
            <div class="w-full sm:w-auto overflow-x-auto">
                <div class="flex justify-center sm:justify-end min-w-max">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
