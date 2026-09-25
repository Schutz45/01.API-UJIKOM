@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

@if (session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
        {{ session('error') }}
    </div>
@endif

<div class="max-w-3xl mx-auto">

{{-- Pesan error validasi --}}
@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
        <p class="font-semibold mb-2">Terjadi kesalahan:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Informasi Peminjaman --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Informasi Peminjaman
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <p class="text-sm text-gray-500">Peminjam</p>
            <p class="font-medium text-gray-800">
                {{ $peminjaman->user->name }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Status</p>

            @if ($peminjaman->status === 'dipinjam')
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700">
                    Dipinjam
                </span>
            @elseif ($peminjaman->status === 'telat')
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                    Telat
                </span>
            @endif
        </div>

        <div>
            <p class="text-sm text-gray-500">Tanggal Pinjam</p>
            <p class="font-medium text-gray-800">
                {{ $peminjaman->tgl_pinjam?->format('d-m-Y') }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Rencana Kembali</p>
            <p class="font-medium text-gray-800">
                {{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') }}
            </p>
        </div>

    </div>
</div>

<form
    action="{{ route('admin.pengembalian.store', $peminjaman->id) }}"
    method="POST"
>
    @csrf

    {{-- Daftar Alat --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Alat yang Dipinjam
    </h2>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-left">
                    <th class="py-3 pr-4 font-semibold text-gray-600">Alat</th>
                    <th class="py-3 pr-4 font-semibold text-gray-600">Jumlah</th>
                    <th class="py-3 font-semibold text-gray-600 text-center kol-rusak hidden">Jumlah Rusak</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($peminjaman->detailPinjam as $detail)
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-4 text-gray-800">{{ $detail->alat->nama_alat }}</td>
                        <td class="py-3 pr-4 text-gray-800">{{ $detail->jumlah }}</td>
                        <td class="py-3 text-center kol-rusak hidden">
                            <input
                                type="number"
                                name="jumlah_rusak[{{ $detail->alat_id }}]"
                                min="0"
                                max="{{ $detail->jumlah }}"
                                class="w-24 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500"
                                placeholder="0"
                            >
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Form Pengembalian --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <h2 class="text-lg font-semibold text-gray-800 mb-6">
        Data Pengembalian
    </h2>

        {{-- Tanggal Kembali --}}
        <div class="mb-5">
            <label
                for="tgl_kembali"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Tanggal Kembali
            </label>

            <input
                type="date"
                id="tgl_kembali"
                name="tgl_kembali"
                value="{{ old('tgl_kembali', now()->format('Y-m-d')) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
            >

            <p class="text-xs text-gray-500 mt-1">
                Rencana kembali:
                {{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') }}
            </p>
        </div>

        {{-- Kondisi Kembali --}}
        <div class="mb-5">
            <label
                for="kondisi_kembali"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Kondisi Alat Saat Dikembalikan
            </label>

            <select
                id="kondisi_kembali"
                name="kondisi_kembali"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
            >
                <option value="">-- Pilih Kondisi --</option>
                <option value="baik" {{ old('kondisi_kembali') === 'baik' ? 'selected' : '' }}>
                    Baik
                </option>
                <option value="rusak" {{ old('kondisi_kembali') === 'rusak' ? 'selected' : '' }}>
                    Rusak
                </option>
            </select>
        </div>

        {{-- Denda Kerusakan --}}
        <div class="mb-5">
            <label
                for="denda_kerusakan"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Denda Kerusakan
            </label>

            <input
                type="number"
                id="denda_kerusakan"
                name="denda_kerusakan"
                value="{{ old('denda_kerusakan', 0) }}"
                min="0"
                step="1000"
                disabled
                class="w-full border border-gray-300 rounded-lg px-3 py-2
                focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                disabled:bg-gray-100 disabled:text-gray-400"
                placeholder="Masukkan nominal denda kerusakan"
            >

            <p class="text-xs text-gray-500 mt-1">
                Isi nominal jika alat dikembalikan dalam kondisi rusak.
            </p>
        </div>

        {{-- Informasi Denda Keterlambatan --}}
        <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">
                    Tarif keterlambatan
                </span>

                <span class="font-semibold text-gray-800">
                    Rp1.000 / hari
                </span>
            </div>

            <p class="text-xs text-gray-500 mt-2">
                Denda keterlambatan akan dihitung otomatis berdasarkan
                tanggal kembali dan tanggal rencana kembali.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('admin.peminjaman.index') }}"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
            >
                Proses Pengembalian
            </button>

        </div>

    </form>
</div>

</div>

<script>
    const kondisiKembali = document.getElementById('kondisi_kembali');
    const dendaKerusakan = document.getElementById('denda_kerusakan');
    const kolRusak = document.querySelectorAll('.kol-rusak');

    function updateKondisi() {
        if (kondisiKembali.value === 'rusak') {
            dendaKerusakan.disabled = false;
            kolRusak.forEach(el => el.classList.remove('hidden'));
        } else {
            dendaKerusakan.disabled = true;
            dendaKerusakan.value = 0;
            kolRusak.forEach(el => el.classList.add('hidden'));
        }
    }

    kondisiKembali.addEventListener('change', updateKondisi);

    updateKondisi();
</script>

@endsection