@extends('layouts.app')

@section('title', 'Edit Alat - Panel Admin')
@section('header-title', 'Edit Data Alat')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route('admin.alat.update', $alat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Alat</label>
            <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

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
                    <option
                        value="{{ $kategori->nama_kategori }}"
                        data-id="{{ $kategori->id }}">
                    </option>
                @endforeach
            </datalist>

            @error('kategori_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', $alat->stok) }}" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Status Kondisi
                </label>

                <select
                    name="status_kondisi"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option
                        value="baik"
                        {{ old('status_kondisi', strtolower($alat->status_kondisi)) === 'baik' ? 'selected' : '' }}
                    >
                        Baik
                    </option>

                    <option
                        value="rusak"
                        {{ old('status_kondisi', strtolower($alat->status_kondisi)) === 'rusak' ? 'selected' : '' }}
                    >
                        Rusak
                    </option>
                </select>

                @error('status_kondisi')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
            <textarea name="deskripsi" id="" cols="" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                {{ old('deskripsi', $alat->deskripsi) }}
            </textarea>
        </div>

        <div class="mb-6">
            <label for="" class="block text-gray-700 text-sm font-semibold mb-2">Gambar Alat <span class="text-xs text-gray-400 font-normal">
                (Biarkan kosong jika tidak ingin mengubah gambar)</span>
            </label>
            @if ($alat->gambar)
                <div class="mb-2">
                    <img src="{{ asset($alat->gambar) }}" alt="Preview" class="w-16 h-16 object-cover rounded-lg border">
                </div>
            @endif
            <input type="file" name="gambar" accept="image/*"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.alat.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>

</div>

<script>
    const kategoriNama = document.getElementById('kategori_nama');
    const kategoriId = document.getElementById('kategori_id');
    const daftarKategori = document.getElementById('daftar-kategori');

    kategoriNama.addEventListener('input', function () {
        const option = Array.from(daftarKategori.options).find(
            option => option.value === this.value
        );

        kategoriId.value = option ? option.dataset.id : '';
    });
</script>
    
@endsection