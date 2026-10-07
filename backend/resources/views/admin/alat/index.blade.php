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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
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
                            <button type="button"
                                onclick="showUnitModal('{{ $alat->nama_alat }}', {{ json_encode($alat->unitAlat) }})"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-blue-700 hover:bg-blue-100 transition border border-blue-200">
                                <i class="bi bi-upc-scan"></i>
                                <span>{{ $alat->unitAlat->count() }} Unit</span>
                            </button>
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

    </div>

    {{-- Modal Daftar Unit Alat --}}
    <div id="unitListModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeUnitModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white" id="modal-alat-title">Daftar Unit Alat</h3>
                        <p class="text-xs text-blue-100" id="modal-alat-subtitle">Daftar nomor seri dan status fisik unit</p>
                    </div>
                    <button type="button" onclick="closeUnitModal()" class="text-white hover:text-gray-200 transition"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="p-6 bg-white max-h-96 overflow-y-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500 text-xs uppercase">
                                <th class="py-2 px-3">No. Seri</th>
                                <th class="py-2 px-3">Status</th>
                                <th class="py-2 px-3">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody id="unit-table-body" class="divide-y divide-gray-100 text-gray-700">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeUnitModal()" class="px-5 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showUnitModal(namaAlat, units) {
            document.getElementById('modal-alat-title').innerText = namaAlat;
            document.getElementById('modal-alat-subtitle').innerText = 'Total ' + units.length + ' unit terdaftar';
            
            const tbody = document.getElementById('unit-table-body');
            tbody.innerHTML = '';
            
            if (units.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-gray-400">Belum ada unit terdaftar.</td></tr>';
            } else {
                units.forEach(unit => {
                    let statusBadge = '';
                    if (unit.status === 'tersedia') statusBadge = '<span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700 font-semibold">Tersedia</span>';
                    else if (unit.status === 'dipinjam') statusBadge = '<span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700 font-semibold">Dipinjam</span>';
                    else statusBadge = '<span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700 font-semibold">' + unit.status + '</span>';

                    let kondisiBadge = unit.kondisi === 'baik' 
                        ? '<span class="text-green-600 font-medium"><i class="bi bi-check-circle-fill mr-1"></i>Baik</span>'
                        : '<span class="text-red-600 font-medium"><i class="bi bi-x-circle-fill mr-1"></i>Rusak</span>';

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 transition';
                    tr.innerHTML = `
                        <td class="py-2.5 px-3 font-mono font-bold text-gray-800">${unit.nomor_seri}</td>
                        <td class="py-2.5 px-3">${statusBadge}</td>
                        <td class="py-2.5 px-3">${kondisiBadge}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
            
            document.getElementById('unitListModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUnitModal() {
            document.getElementById('unitListModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
