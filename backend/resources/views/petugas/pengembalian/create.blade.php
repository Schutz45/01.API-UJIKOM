@extends('layouts.app')

@section('title', 'Proses Pengembalian - Dashboard Petugas')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">
                    Form Pengembalian Alat
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Periksa data peminjaman dan masukkan kondisi alat saat dikembalikan.
                </p>
            </div>


            {{-- Informasi Peminjaman --}}
            <div class="p-5">

                <h4 class="text-sm font-bold text-gray-700 mb-4">
                    Informasi Peminjaman
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                    {{-- Peminjam --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Peminjam
                        </label>

                        <input
                            type="text"
                            value="{{ $peminjaman->user->name ?? '-' }}"
                            readonly
                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Status Peminjaman
                        </label>

                        <input
                            type="text"
                            value="{{ ucfirst($peminjaman->status) }}"
                            readonly
                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                    </div>

                    {{-- Tanggal Pinjam --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Tanggal Pinjam
                        </label>

                        <input
                            type="text"
                            value="{{ $peminjaman->tgl_pinjam }}"
                            readonly
                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                    </div>

                    {{-- Batas Kembali --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Batas Kembali
                        </label>

                        <input
                            type="text"
                            value="{{ $peminjaman->tgl_kembali_plan }}"
                            readonly
                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                    </div>

                </div>


                {{-- Detail Alat --}}
                <div class="mb-6">

                    <h4 class="text-sm font-bold text-gray-700 mb-3">
                        Alat yang Dipinjam
                    </h4>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">

                        <table class="w-full text-left text-sm">

                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 border-b">
                                        Nama Alat
                                    </th>

                                    <th class="px-4 py-3 border-b text-center">
                                        Jumlah
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($peminjaman->detailPinjam as $detail)

                                    <tr>
                                        <td class="px-4 py-3 border-b">
                                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                        </td>

                                        <td class="px-4 py-3 border-b text-center">
                                            {{ $detail->jumlah }}
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- Form Pengembalian --}}
                <form
                    action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}"
                    method="POST">

                    @csrf

                    <h4 class="text-sm font-bold text-gray-700 mb-4">
                        Data Pengembalian
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Tanggal Kembali --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Kembali
                            </label>

                            <input
                                type="date"
                                value="{{ now()->format('Y-m-d') }}"
                                readonly
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                        </div>

                        {{-- Kondisi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kondisi Alat Saat Kembali
                            </label>

                            <select
                                name="kondisi_kembali"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">

                                <option value="">-- Pilih Kondisi --</option>
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>

                            </select>
                        </div>

                        {{-- Denda --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Denda Kerusakan
                            </label>

                            <div class="relative">

                                <span class="absolute left-3 top-2 text-gray-500 text-sm">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="denda"
                                    min="0"
                                    value="0"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Masukkan nominal denda jika terdapat keterlambatan atau kerusakan.
                                Denda keterlambatan akan dihitung otomatis sebesar Rp 1.000 per hari.
                            </p>

                        </div>

                    </div>


                    {{-- Tombol --}}
                    <div class="flex justify-end gap-2 mt-6">

                        <a
                            href="{{ route('petugas.pengembalian.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Simpan Pengembalian
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection