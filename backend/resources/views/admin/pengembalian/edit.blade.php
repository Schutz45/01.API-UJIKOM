@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Pengembalian Alat')

@section('content')

<div class="max-w-4xl mx-auto">

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-bold text-gray-800">
            Edit Data Pengembalian
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Periksa dan perbaiki data pengembalian jika diperlukan.
        </p>
    </div>

    {{-- Error Validasi --}}
    @if($errors->any())
        <div class="m-5 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form
        action="{{ route('admin.pengembalian.update', $pengembalian->id) }}"
        method="POST"
        class="p-5 space-y-6"
    >

        @csrf
        @method('PUT')

        {{-- Informasi Peminjaman --}}
        <div>
            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">
                Informasi Peminjaman
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Peminjam --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Peminjam
                    </label>

                    <input
                        type="text"
                        value="{{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}"
                        readonly
                        class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                    >
                </div>

                {{-- Petugas --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Petugas
                    </label>

                    <input
                        type="text"
                        value="{{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}"
                        readonly
                        class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                    >
                </div>

                {{-- Tanggal Pinjam --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Tanggal Pinjam
                    </label>

                    <input
                        type="text"
                        value="{{ $pengembalian->peminjaman->tgl_pinjam?->format('d-m-Y') }}"
                        readonly
                        class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                    >
                </div>

                {{-- Rencana Kembali --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Rencana Kembali
                    </label>

                    <input
                        type="text"
                        value="{{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d-m-Y') }}"
                        readonly
                        class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                    >
                </div>

            </div>
        </div>

        {{-- Daftar Alat --}}
        <div>
            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                Alat yang Dikembalikan
            </h4>

            <div class="border border-gray-200 rounded-lg overflow-hidden">

                <table class="w-full text-left">

                    <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="py-2 px-3">
                                Alat
                            </th>

                            <th class="py-2 px-3 text-center">
                                Jumlah
                            </th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-gray-700">

                        @foreach($pengembalian->peminjaman->detailPinjam as $detail)

                            <tr class="border-t border-gray-200">

                                <td class="py-2 px-3 font-medium">
                                    {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                </td>

                                <td class="py-2 px-3 text-center">
                                    {{ $detail->jumlah }} pcs
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

        {{-- Data Pengembalian --}}
        <div>
            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">
                Data Pengembalian
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Tanggal Kembali --}}
                <div>
                    <label
                        for="tgl_kembali"
                        class="block text-sm font-semibold text-gray-700 mb-1"
                    >
                        Tanggal Kembali
                    </label>

                    <input
                        type="date"
                        id="tgl_kembali"
                        name="tgl_kembali"
                        value="{{ old('tgl_kembali', $pengembalian->tgl_kembali?->format('Y-m-d')) }}"
                        min="{{ $pengembalian->peminjaman->tgl_pinjam?->format('Y-m-d') }}"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                {{-- Kondisi --}}
                <div>
                    <label
                        for="kondisi_kembali"
                        class="block text-sm font-semibold text-gray-700 mb-1"
                    >
                        Kondisi Alat
                    </label>

                    <select
                        id="kondisi_kembali"
                        name="kondisi_kembali"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="baik"
                            {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) === 'baik' ? 'selected' : '' }}>
                            Baik
                        </option>

                        <option value="rusak"
                            {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) === 'rusak' ? 'selected' : '' }}>
                            Rusak
                        </option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Denda Kerusakan --}}
        <div>

            <label
                for="denda_kerusakan"
                class="block text-sm font-semibold text-gray-700 mb-1"
            >
                Denda Kerusakan
            </label>

            <div class="relative">

                <span class="absolute left-3 top-2 text-sm text-gray-500">
                    Rp
                </span>

                <input
                    type="number"
                    id="denda_kerusakan"
                    name="denda_kerusakan"
                    min="0"
                    value="{{ old('denda_kerusakan', $pengembalian->denda_kerusakan) }}"
                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <p class="text-xs text-gray-500 mt-1">
                Isi nominal denda kerusakan jika kondisi alat rusak. Denda keterlambatan dihitung otomatis.
            </p>

        </div>

        {{-- Informasi Denda Saat Ini --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">

            <div class="flex justify-between items-center">

                <span class="text-sm font-semibold text-gray-700">
                    Denda Tercatat Saat Ini
                </span>

                <span class="text-lg font-bold text-gray-800">
                    Rp{{ number_format($pengembalian->denda, 0, ',', '.') }}
                </span>

            </div>

            <p class="text-xs text-gray-500 mt-1">
                Nilai denda akan dihitung ulang ketika data disimpan.
            </p>

        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">

            <a
                href="{{ route('admin.pengembalian.index') }}"
                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

</div>

@endsection
