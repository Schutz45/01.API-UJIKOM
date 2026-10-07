@extends('layouts.app')

@section('title', 'Detail Pengembalian - Panel Admin')
@section('header-title', 'Detail Pengembalian Alat')

@section('content')

<div class="max-w-4xl mx-auto">

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Detail Pengembalian
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Transaksi pengembalian ini sudah final dan tidak dapat diubah.
                </p>
            </div>

            <a href="{{ route('admin.pengembalian.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-white transition text-sm font-semibold">
                Kembali
            </a>
        </div>

        {{-- Informasi Peminjaman --}}
        <div class="p-6">
            <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">
                Informasi Peminjaman
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500">Peminjam</p>
                    <p class="font-medium text-gray-800">
                        {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Status Peminjaman</p>
                    @if ($pengembalian->peminjaman->status === 'dikembalikan')
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-emerald-100 text-emerald-700">
                            Dikembalikan
                        </span>
                    @else
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700">
                            {{ ucfirst($pengembalian->peminjaman->status) }}
                        </span>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-gray-500">Tanggal Pinjam</p>
                    <p class="font-medium text-gray-800">
                        {{ $pengembalian->peminjaman->tgl_pinjam?->format('d-m-Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Rencana Kembali</p>
                    <p class="font-medium text-gray-800">
                        {{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d-m-Y') }}
                    </p>
                </div>
            </div>

            {{-- Alat --}}
            <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">
                Alat yang Dikembalikan
            </h4>

            <div class="overflow-x-auto border border-gray-200 rounded-lg mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="py-3 px-4 text-left font-semibold border-b">Alat</th>
                            <th class="py-3 px-4 text-center font-semibold border-b">Dipinjam</th>
                            <th class="py-3 px-4 text-center font-semibold border-b">Baik</th>
                            <th class="py-3 px-4 text-center font-semibold border-b">Rusak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengembalian->peminjaman->detailPinjam as $detail)
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="py-3 px-4 text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</td>
                                <td class="py-3 px-4 text-center font-medium text-gray-800">{{ $detail->jumlah }}</td>
                                <td class="py-3 px-4 text-center font-medium text-emerald-700">
                                    {{ $detail->unitAlat->where('pivot.kondisi_masuk', 'baik')->count() }}
                                </td>
                                <td class="py-3 px-4 text-center font-medium text-red-700">
                                    {{ $detail->unitAlat->where('pivot.kondisi_masuk', 'rusak')->count() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">Tidak ada data alat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rincian Denda --}}
            <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">
                Rincian Denda
            </h4>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal Dikembalikan</span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->format('d-m-Y') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Kondisi Pengembalian</span>
                    <span class="font-medium text-gray-800 capitalize">
                        {{ $pengembalian->kondisi_kembali }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Denda Keterlambatan</span>
                    <span class="font-medium text-gray-800">
                        Rp{{ number_format($pengembalian->denda_keterlambatan ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Denda Kerusakan</span>
                    <span class="font-medium text-red-600">
                        Rp{{ number_format($pengembalian->denda_kerusakan ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div class="pt-3 border-t border-gray-300 flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total Denda</span>
                    <span class="text-xl font-bold text-blue-700">
                        Rp{{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Petugas --}}
            <div class="mt-6 flex items-center gap-3 text-sm text-gray-600">
                <i class="bi bi-person-badge text-lg text-gray-400"></i>
                Diproses oleh:
                <span class="font-medium text-gray-800">
                    {{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}
                </span>
            </div>
        </div>

    </div>

</div>

@endsection
