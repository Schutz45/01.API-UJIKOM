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

                    <div class="space-y-4 mb-6">
                        @foreach($peminjaman->detailPinjam as $detail)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                    <span class="text-xs text-gray-500">{{ $detail->jumlah }} unit</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($detail->unitAlat as $unit)
                                        <div class="unit-card flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 transition-all duration-200">
                                            <span class="font-mono text-sm">{{ $unit->nomor_seri }}</span>
                                            <div class="flex gap-3 text-sm">
                                                <label class="flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="unit_kondisi[{{ $unit->id }}]" value="baik" required class="text-emerald-600 focus:ring-emerald-500"> Baik
                                                </label>
                                                <label class="flex items-center gap-1 cursor-pointer text-red-600">
                                                    <input type="radio" name="unit_kondisi[{{ $unit->id }}]" value="rusak" class="text-red-600 focus:ring-red-500"> Rusak
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
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

                        {{-- Denda --}}
                        <div id="wrapper_denda">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Denda Kerusakan
                                <span id="denda_wajib_label" class="text-red-600 font-bold hidden">* WAJIB</span>
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
                                    step="1000"
                                    value="{{ old('denda') }}"
                                    disabled
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-100 disabled:text-gray-400"
                                    placeholder="Belum ada unit rusak">

                            </div>

                            <p id="denda_catatan" class="text-xs text-gray-500 mt-1">
                                Kolom denda aktif setelah ada unit yang dipilih Rusak.
                            </p>

                            @error('denda')
                                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror

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
                            id="btnProsesPengembalian"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="btnText">Simpan Pengembalian</span>
                            <span id="btnSpinner" class="hidden items-center gap-2">
                                <i class="bi bi-arrow-repeat animate-spin"></i> Memproses...
                            </span>
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dendaKerusakanInput = document.getElementById('denda_kerusakan');
        const tglKembaliInput = document.getElementById('tgl_kembali');
        const radiosKondisi = document.querySelectorAll('input[name^="unit_kondisi["]');
        const dendaWajibLabel = document.getElementById('denda_wajib_label');
        const dendaCatatan = document.getElementById('denda_catatan');

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

        // Hitung jumlah unit yang dipilih "Rusak"
        function hitungUnitRusak() {
            let rusak = 0;
            radiosKondisi.forEach(el => {
                if (el.value === 'rusak' && el.checked) rusak++;
            });
            return rusak;
        }

        // Highlight kartu unit sesuai pilihan kondisi (baik=hijau, rusak=merah)
        function highlightUnitCards() {
            radiosKondisi.forEach(el => {
                const card = el.closest('.unit-card');
                if (!card) return;

                if (el.checked) {
                    if (el.value === 'rusak') {
                        card.classList.add('bg-red-50', 'border-red-300');
                        card.classList.remove('bg-emerald-50', 'border-emerald-300');
                    } else {
                        card.classList.add('bg-emerald-50', 'border-emerald-300');
                        card.classList.remove('bg-red-50', 'border-red-300');
                    }
                }
            });
        }

        // Aktifkan/nonaktifkan kewajiban field denda
        function syncWajibDenda() {
            const adaRusak = hitungUnitRusak() > 0;

            if (adaRusak) {
                dendaKerusakanInput.disabled = false;
                dendaKerusakanInput.required = true;
                dendaKerusakanInput.min = 1000;
                dendaWajibLabel.classList.remove('hidden');
                dendaCatatan.innerText = 'Wajib diisi minimal Rp1.000 karena ada unit yang dikembalikan rusak.';
                dendaCatatan.className = 'text-xs text-red-600 font-medium mt-1';
                dendaKerusakanInput.classList.add('border-red-400');
                dendaKerusakanInput.classList.remove('disabled:bg-gray-100', 'disabled:text-gray-400');
            } else {
                dendaKerusakanInput.disabled = true;
                dendaKerusakanInput.required = false;
                dendaKerusakanInput.min = 0;
                dendaKerusakanInput.value = '';
                dendaWajibLabel.classList.add('hidden');
                dendaCatatan.innerText = 'Tidak ada unit rusak, tidak ada denda kerusakan.';
                dendaCatatan.className = 'text-xs text-gray-500 mt-1';
                dendaKerusakanInput.classList.remove('border-red-400');
                dendaKerusakanInput.classList.add('disabled:bg-gray-100', 'disabled:text-gray-400');
            }

            calculateDenda();
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

            if (dendaKerusakan > 0) {
                rowDendaRusak.classList.remove('hidden');
                labelDendaRusak.innerText = formatRupiah(dendaKerusakan);
            } else {
                rowDendaRusak.classList.add('hidden');
            }

            labelTotalDenda.innerText = formatRupiah(dendaTelat + dendaKerusakan);
        }

        radiosKondisi.forEach(el => el.addEventListener('change', function() {
            syncWajibDenda();
            highlightUnitCards();
        }));
        dendaKerusakanInput.addEventListener('input', calculateDenda);
        tglKembaliInput.addEventListener('change', calculateDenda);

        const formPetugas = document.querySelector('form[action*="pengembalian"]');
        if (formPetugas) {
            formPetugas.addEventListener('submit', function() {
                const btn = document.getElementById('btnProsesPengembalian');
                const btnText = document.getElementById('btnText');
                const btnSpinner = document.getElementById('btnSpinner');
                if (btn && btnText && btnSpinner) {
                    btn.disabled = true;
                    btnText.classList.add('hidden');
                    btnSpinner.classList.remove('hidden');
                    btnSpinner.classList.add('inline-flex');
                }
            });
        }

        syncWajibDenda();
    });
</script>
@endsection
