@extends('layouts.app')

@section('title', 'Edit Alat - Panel Admin')
@section('header-title', 'Edit Data Alat')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route('admin.alat.update', $alat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nama Alat --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nama Alat
            </label>
            <input
                type="text"
                name="nama_alat"
                value="{{ old('nama_alat', $alat->nama_alat) }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kategori
            </label>
            <input
                type="text"
                id="kategori_nama"
                list="daftar-kategori"
                value="{{ old('kategori_nama', $alat->kategori->nama_kategori) }}"
                required
                placeholder="Cari atau pilih kategori..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <input
                type="hidden"
                name="kategori_id"
                id="kategori_id"
                value="{{ old('kategori_id', $alat->kategori_id) }}"
            >
            <datalist id="daftar-kategori">
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->nama_kategori }}" data-id="{{ $kategori->id }}"></option>
                @endforeach
            </datalist>
            @error('kategori_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Pusat Pengelolaan Kondisi & Stok (Readonly/Display + Aksi Cepat) --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center justify-between">
                <span>Pengelolaan Kondisi & Stok Unit</span>
                <span class="px-2.5 py-1 text-xs rounded-full font-semibold
                    @if($alat->status_kondisi == 'baik') bg-green-100 text-green-800
                    @elseif($alat->status_kondisi == 'sebagian_rusak') bg-yellow-100 text-yellow-800
                    @elseif($alat->status_kondisi == 'rusak') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    Status: {{ $alat->status_kondisi_label }}
                </span>
            </h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-white p-3 rounded border border-gray-200">
                    <span class="block text-xs text-gray-500 font-medium">Stok Baik (Tersedia)</span>
                    <span class="text-xl font-bold text-green-600">{{ $alat->jumlah_tersedia }} unit</span>
                </div>
                <div class="bg-white p-3 rounded border border-gray-200">
                    <span class="block text-xs text-gray-500 font-medium">Stok Rusak</span>
                    <span class="text-xl font-bold text-red-600">{{ $alat->jumlah_rusak }} unit</span>
                </div>
            </div>

            {{-- Input Stok Utama (Hanya tampilan, tidak bisa diedit manual) --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Stok Baik (Tersedia)
                </label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700 font-semibold">
                    {{ $alat->jumlah_tersedia }} unit
                    <span class="block text-xs text-gray-400 font-normal mt-0.5">
                        Stok hanya dapat diubah melalui Tandai Rusak / Perbaiki Alat / proses peminjaman.
                    </span>
                </div>
            </div>

            {{-- Tombol Aksi Cepat: Tandai Rusak & Perbaiki Alat --}}
            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                @if($alat->jumlah_tersedia > 0)
                @php
                    $unitTersedia = $alat->unitAlat->where('status', 'tersedia')->map(fn($u) => ['id' => $u->id, 'nomor_seri' => $u->nomor_seri])->values();
                @endphp
                <button
                    type="button"
                    data-title="Tandai Alat Rusak"
                    data-subtitle="Pilih unit yang mengalami kerusakan:"
                    data-units="{{ json_encode($unitTersedia) }}"
                    data-action="{{ route('admin.alat.tandai-rusak', $alat->id) }}"
                    onclick="bukaModalUnit(this)"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1"
                >
                    <i class="bi bi-exclamation-triangle"></i> Tandai Rusak
                </button>
                @endif

                @if($alat->jumlah_rusak > 0)
                @php
                    $unitRusak = $alat->unitAlat->where('kondisi', 'rusak')->map(fn($u) => ['id' => $u->id, 'nomor_seri' => $u->nomor_seri])->values();
                @endphp
                <button
                    type="button"
                    data-title="Perbaiki Alat Rusak"
                    data-subtitle="Pilih unit yang selesai diperbaiki:"
                    data-units="{{ json_encode($unitRusak) }}"
                    data-action="{{ route('admin.alat.perbaiki', $alat->id) }}"
                    onclick="bukaModalUnit(this)"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1"
                >
                    <i class="bi bi-tools"></i> Perbaiki Alat
                </button>
                @endif
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Deskripsi
            </label>
            <textarea
                name="deskripsi"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >{{ old('deskripsi', $alat->deskripsi) }}</textarea>
        </div>

        {{-- Gambar --}}
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Gambar Alat
                <span class="text-xs text-gray-400 font-normal">
                    (Biarkan kosong jika tidak ingin mengubah gambar)
                </span>
            </label>
            @if ($alat->gambar)
                <div class="mb-2">
                    <img src="{{ asset($alat->gambar) }}" alt="Preview" class="w-16 h-16 object-cover rounded-lg border">
                </div>
            @endif
            <input
                type="file"
                name="gambar"
                accept="image/*"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            >
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end space-x-2">
            <a
                href="{{ route('admin.alat.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition"
            >
                Batal
            </a>
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
            >
                Perbarui Data Dasar
            </button>
        </div>
    </form>

</div>

{{-- Modal Pemilihan Unit (Tandai Rusak / Perbaiki) --}}
<div id="modalUnit" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" aria-hidden="true" onclick="tutupModalUnit()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-white" id="modalUnitTitle">Tandai Alat Rusak</h3>
                    <p class="text-xs text-amber-50" id="modalUnitSubtitle">Pilih unit yang mengalami kerusakan:</p>
                </div>
                <button type="button" onclick="tutupModalUnit()" class="text-white hover:text-gray-200 transition"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="p-6">
                <div id="unitList" class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-72 overflow-y-auto"></div>
                <p id="unitCount" class="text-center text-sm text-gray-500 mt-4">0 unit dipilih</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-2">
                <button type="button" onclick="tutupModalUnit()" class="px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">Batal</button>
                <button type="button" id="modalUnitSubmit" onclick="submitModalUnit()" class="px-5 py-2 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition shadow-sm">Tandai Rusak</button>
            </div>
        </div>
    </div>
</div>

{{-- Form terpisah untuk submit unit yang dipilih --}}
<form id="formUnit" method="POST" class="hidden">
    @csrf
    <div id="unitIdsContainer"></div>
</form>

{{-- JavaScript --}}
<script>
    // Pencarian Kategori
    const kategoriNama = document.getElementById('kategori_nama');
    const kategoriId = document.getElementById('kategori_id');
    const daftarKategori = document.getElementById('daftar-kategori');

    kategoriNama.addEventListener('input', function () {
        const option = Array.from(daftarKategori.options).find(
            option => option.value === this.value
        );
        kategoriId.value = option ? option.dataset.id : '';
    });

    // Modal Pemilihan Unit (Tandai Rusak / Perbaiki)
    let unitActionUrl = '';

    function bukaModalUnit(btn) {
        const title    = btn.dataset.title;
        const subtitle = btn.dataset.subtitle;
        let units      = [];
        try { units = JSON.parse(btn.dataset.units); } catch (e) { units = []; }

        unitActionUrl = btn.dataset.action;

        document.getElementById('modalUnitTitle').innerText = title;
        document.getElementById('modalUnitSubtitle').innerText = subtitle;

        const container = document.getElementById('unitList');
        container.innerHTML = '';

        // Reset pilihan sebelumnya
        document.getElementById('unitIdsContainer').innerHTML = '';

        units.forEach(unit => {
            const label = document.createElement('label');
            label.className = 'inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono cursor-pointer hover:bg-amber-50 hover:border-amber-200 transition has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 has-[:checked]:text-amber-700';
            label.innerHTML = `
                <input type="checkbox" value="${unit.id}"
                    class="rounded text-amber-600 focus:ring-amber-500 border-gray-300 w-4 h-4"
                    onchange="toggleUnitPilihan(this)">
                <span>${unit.nomor_seri}</span>
            `;
            container.appendChild(label);
        });

        updateUnitCount();

        // Sesuaikan warna tombol submit
        const submitBtn = document.getElementById('modalUnitSubmit');
        if (title.includes('Perbaiki')) {
            submitBtn.className = 'px-5 py-2 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition shadow-sm';
            submitBtn.innerText = 'Perbaiki Unit';
        } else {
            submitBtn.className = 'px-5 py-2 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition shadow-sm';
            submitBtn.innerText = 'Tandai Rusak';
        }

        document.getElementById('modalUnit').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalUnit() {
        document.getElementById('modalUnit').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function toggleUnitPilihan(checkbox) {
        const container = document.getElementById('unitIdsContainer');

        if (checkbox.checked) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'unit_ids[]';
            input.value = checkbox.value;
            input.dataset.unitId = checkbox.value;
            container.appendChild(input);
        } else {
            const existing = container.querySelector(`input[data-unit-id="${checkbox.value}"]`);
            if (existing) existing.remove();
        }

        updateUnitCount();
    }

    function updateUnitCount() {
        const dipilih = document.querySelectorAll('#unitList input[type="checkbox"]:checked').length;
        document.getElementById('unitCount').innerText = dipilih + ' unit dipilih';
    }

    function submitModalUnit() {
        const dipilih = document.querySelectorAll('#unitList input[type="checkbox"]:checked').length;
        if (dipilih === 0) {
            alert('Pilih minimal satu unit terlebih dahulu.');
            return;
        }

        document.getElementById('formUnit').action = unitActionUrl;
        document.getElementById('formUnit').submit();
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') tutupModalUnit();
    });
</script>

@endsection
