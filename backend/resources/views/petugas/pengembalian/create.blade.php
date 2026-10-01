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


            {{-- Detail Alat + Form --}}
            <div class="p-5">

                <form
                    action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}"
                    method="POST">

                    @csrf

                    <h4 class="text-sm font-bold text-gray-700 mb-3">
                        Alat yang Dipinjam
                    </h4>

                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">

                        <table class="w-full text-left text-sm">

                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 border-b">Nama Alat</th>
                                    <th class="px-4 py-3 border-b text-center">Dipinjam</th>
                                    <th class="px-4 py-3 border-b text-center kol-rusak hidden">Jumlah Rusak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <tr>
                                        <td class="px-4 py-3 border-b font-medium">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</td>
                                        <td class="px-4 py-3 border-b text-center font-bold">{{ $detail->jumlah }}</td>
                                        <td class="px-4 py-3 border-b text-center kol-rusak hidden">
                                            <input type="number" 
                                                   name="jumlah_rusak[{{ $detail->alat_id }}]" 
                                                   min="0" 
                                                   max="{{ $detail->jumlah }}" 
                                                   placeholder="{{ $detail->jumlah }}"
                                                   class="w-24 px-2 py-1 text-center border border-red-300 rounded focus:ring-red-500 focus:border-red-500">
                                            <span class="block text-[10px] text-gray-500 mt-0.5">Maks: {{ $detail->jumlah }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

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
                                id="tgl_kembali"
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
                                    id="denda_kerusakan"
                                    min="0"
                                    value="0"
                                    disabled
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-100 disabled:text-gray-400">

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Isi nominal denda kerusakan hanya jika alat dikembalikan dalam kondisi rusak.
                            </p>

                        </div>

                    </div>

                    {{-- Rincian Biaya & Denda (read-only) --}}
                    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-3">
                        <h4 class="text-sm font-bold text-gray-700 border-b border-gray-200 pb-2 mb-3">
                            Rincian Biaya & Denda
                        </h4>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Keterlambatan</span>
                            <span id="label_hari_telat" class="font-medium text-gray-800">0 hari</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tarif Denda Keterlambatan</span>
                            <span class="font-medium text-gray-800">Rp1.000 / hari</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Denda Keterlambatan</span>
                            <span id="label_denda_telat" class="font-medium text-gray-800">Rp0</span>
                        </div>

                        <div id="row_denda_rusak" class="flex justify-between text-sm hidden">
                            <span class="text-gray-600">Denda Kerusakan</span>
                            <span id="label_denda_rusak" class="font-medium text-red-600">Rp0</span>
                        </div>

                        <div class="pt-2 border-t border-gray-200 flex justify-between items-center">
                            <span class="font-bold text-gray-800 text-base">Total Denda</span>
                            <span id="label_total_denda" class="text-xl font-bold text-emerald-700">Rp0</span>
                        </div>

                        <p class="text-[11px] text-gray-500 italic pt-2">
                            * Denda keterlambatan dihitung otomatis dari selisih tanggal kembali dan tanggal rencana kembali.
                        </p>
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kondisiSelect = document.querySelector('select[name="kondisi_kembali"]');
        const kolRusak = document.querySelectorAll('.kol-rusak');
        const dendaKerusakanInput = document.getElementById('denda_kerusakan');
        const tglKembaliInput = document.getElementById('tgl_kembali');

        // Data dari backend
        const tglRencanaKembali = new Date("{{ $peminjaman->tgl_kembali_plan }}");

        // Elemen label hasil perhitungan
        const labelHariTelat = document.getElementById('label_hari_telat');
        const labelDendaTelat = document.getElementById('label_denda_telat');
        const rowDendaRusak = document.getElementById('row_denda_rusak');
        const labelDendaRusak = document.getElementById('label_denda_rusak');
        const labelTotalDenda = document.getElementById('label_total_denda');

        function formatRupiah(number) {
            return 'Rp' + number.toLocaleString('id-ID');
        }

        function calculateDenda() {
            // 1. Hitung Keterlambatan
            const tglKembali = new Date(tglKembaliInput.value + 'T00:00:00');
            let diffTime = tglKembali - tglRencanaKembali;
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            let hariTelat = diffDays > 0 ? diffDays : 0;
            let dendaTelat = hariTelat * 1000;

            // 2. Hitung Kerusakan
            let dendaKerusakan = parseInt(dendaKerusakanInput.value) || 0;

            // 3. Update Label
            labelHariTelat.innerText = `${hariTelat} hari`;
            labelDendaTelat.innerText = formatRupiah(dendaTelat);

            if (kondisiSelect.value === 'rusak') {
                rowDendaRusak.classList.remove('hidden');
                labelDendaRusak.innerText = formatRupiah(dendaKerusakan);
            } else {
                rowDendaRusak.classList.add('hidden');
                dendaKerusakan = 0;
            }

            labelTotalDenda.innerText = formatRupiah(dendaTelat + dendaKerusakan);
        }

        function toggleRusak() {
            if (kondisiSelect.value === 'rusak') {
                kolRusak.forEach(el => el.classList.remove('hidden'));
                dendaKerusakanInput.disabled = false;
            } else {
                kolRusak.forEach(el => el.classList.add('hidden'));
                dendaKerusakanInput.disabled = true;
                dendaKerusakanInput.value = 0;
            }
            calculateDenda();
        }

        kondisiSelect.addEventListener('change', toggleRusak);
        dendaKerusakanInput.addEventListener('input', calculateDenda);
        tglKembaliInput.addEventListener('change', calculateDenda);

        toggleRusak();
    });
</script>
@endsection
