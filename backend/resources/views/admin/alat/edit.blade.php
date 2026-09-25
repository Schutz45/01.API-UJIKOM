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
                    <span class="text-xl font-bold text-green-600">{{ $alat->stok }} unit</span>
                </div>
                <div class="bg-white p-3 rounded border border-gray-200">
                    <span class="block text-xs text-gray-500 font-medium">Stok Rusak</span>
                    <span class="text-xl font-bold text-red-600">{{ $alat->stok_rusak }} unit</span>
                </div>
            </div>

            {{-- Input Stok Utama (Hanya tampilan, tidak bisa diedit manual) --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Stok Baik (Tersedia)
                </label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700 font-semibold">
                    {{ $alat->stok }} unit
                    <span class="block text-xs text-gray-400 font-normal mt-0.5">
                        Stok hanya dapat diubah melalui Tandai Rusak / Perbaiki Alat / proses peminjaman.
                    </span>
                </div>
            </div>

            {{-- Tombol Aksi Cepat: Tandai Rusak & Perbaiki Alat --}}
            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                @if($alat->stok > 0)
                <button
                    type="button"
                    onclick="bukaModalAksi('rusak', {{ $alat->stok }}, '{{ route('admin.alat.tandai-rusak', $alat->id) }}')"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1"
                >
                    <i class="bi bi-exclamation-triangle"></i> Tandai Rusak
                </button>
                @endif

                @if($alat->stok_rusak > 0)
                <button
                    type="button"
                    onclick="bukaModalAksi('perbaiki', {{ $alat->stok_rusak }}, '{{ route('admin.alat.perbaiki', $alat->id) }}')"
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

{{-- Modal Aksi Konfirmasi (Tandai Rusak / Perbaiki) --}}
<div id="modalAksi" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-lg">
        <h3 id="modalTitle" class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Aksi</h3>
        <p id="modalDesc" class="text-sm text-gray-600 mb-4">Masukkan jumlah unit:</p>

        <form id="formAksi" method="POST">
            @csrf
            <input
                type="number"
                name="jumlah"
                id="inputJumlah"
                min="1"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Jumlah unit..."
            >
            <div class="flex justify-end space-x-2">
                <button
                    type="button"
                    onclick="tutupModalAksi()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1.5 rounded-lg text-sm font-semibold"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="modalBtnSubmit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold"
                >
                    Proses
                </button>
            </div>
        </form>
    </div>
</div>

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

    // Modal Aksi Rusak / Perbaiki
    function bukaModalAksi(tipe, maxVal, url) {
        const modal = document.getElementById('modalAksi');
        const title = document.getElementById('modalTitle');
        const desc = document.getElementById('modalDesc');
        const form = document.getElementById('formAksi');
        const input = document.getElementById('inputJumlah');
        const btnSubmit = document.getElementById('modalBtnSubmit');

        form.action = url;
        input.max = maxVal;
        input.value = 1;

        if (tipe === 'rusak') {
            title.innerText = 'Tandai Alat Rusak';
            desc.innerText = `Masukkan jumlah unit yang rusak (Maks: ${maxVal}):`;
            btnSubmit.className = 'bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold';
        } else {
            title.innerText = 'Perbaiki Alat Rusak';
            desc.innerText = `Masukkan jumlah unit yang selesai diperbaiki (Maks: ${maxVal}):`;
            btnSubmit.className = 'bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold';
        }

        modal.classList.remove('hidden');
    }

    function tutupModalAksi() {
        document.getElementById('modalAksi').classList.add('hidden');
    }
</script>

@endsection
