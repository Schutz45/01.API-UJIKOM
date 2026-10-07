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
                    <th class="py-3 font-semibold text-gray-600">Pilih Unit Rusak</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($peminjaman->detailPinjam as $detail)
                    @php
                        $unitsJson = $detail->unitAlat->map(fn($u) => ['id' => $u->id, 'nomor_seri' => $u->nomor_seri])->values();
                    @endphp
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-4 text-gray-800 font-medium">{{ $detail->alat->nama_alat }}</td>
                        <td class="py-3 pr-4 text-gray-800">{{ $detail->jumlah }}</td>
                        <td class="py-3">
                            <button type="button"
                                data-alat="{{ $detail->alat_id }}"
                                data-nama="{{ $detail->alat->nama_alat }}"
                                data-units="{{ json_encode($unitsJson) }}"
                                onclick="openUnitRusakModal(this)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition border border-red-200">
                                <i class="bi bi-upc-scan"></i>
                                Pilih Unit
                            </button>
                            <p id="summary-{{ $detail->alat_id }}" class="text-[11px] text-gray-500 mt-1.5">Semua unit baik</p>
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
        <input type="hidden" name="kondisi_kembali" value="rusak">

        {{-- Denda Kerusakan --}}
        <div class="mb-5">
            <label
                for="denda_kerusakan"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Total Denda Kerusakan
            </label>

            <input
                type="number"
                id="denda_kerusakan"
                name="denda_kerusakan"
                value="{{ old('denda_kerusakan', 0) }}"
                min="0"
                step="1000"
                class="w-full border border-gray-300 rounded-lg px-3 py-2
                focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nominal denda kerusakan"
                required
            >
        </div>

        {{-- Informasi Denda Keterlambatan --}}
        <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-3">
            <h3 class="text-sm font-bold text-gray-700 border-b border-gray-200 pb-2 mb-3 flex items-center gap-2">
                <i class="bi bi-calculator"></i> Rincian Biaya & Denda
            </h3>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Keterlambatan</span>
                <span id="label_hari_telat" class="font-medium text-gray-800">0 hari</span>
            </div>

            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Denda Keterlambatan (Rp1.000 / hari)</span>
                <span id="label_denda_telat" class="font-medium text-gray-800">Rp0</span>
            </div>

            <div id="row_denda_rusak" class="flex justify-between text-sm hidden">
                <span class="text-gray-600">Denda Kerusakan</span>
                <span id="label_denda_rusak" class="font-medium text-red-600">Rp0</span>
            </div>

            <div class="pt-2 border-t border-gray-200 flex justify-between items-center">
                <span class="font-bold text-gray-800 text-base">Total Keseluruhan Denda</span>
                <span id="label_total_denda" class="text-xl font-bold text-blue-700">Rp0</span>
            </div>

            <p class="text-[11px] text-gray-500 italic pt-2">
                * Denda keterlambatan dihitung otomatis dari selisih tanggal kembali dan rencana kembali.
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
const dendaKerusakanInput = document.getElementById('denda_kerusakan');
const tglKembaliInput = document.getElementById('tgl_kembali');
    
// Data dari backend
const tglRencanaKembali = new Date("{{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') }}");
    
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
    const tglKembali = new Date(tglKembaliInput.value);
    let diffTime = tglKembali - tglRencanaKembali;
    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    let hariTelat = diffDays > 0 ? diffDays : 0;
    let dendaTelat = hariTelat * 1000;

    // 2. Hitung Kerusakan
    let dendaKerusakan = parseInt(dendaKerusakanInput.value) || 0;

    // 3. Update Label
    labelHariTelat.innerText = `${hariTelat} hari`;
    labelDendaTelat.innerText = formatRupiah(dendaTelat);
        
    rowDendaRusak.classList.remove('hidden');
    labelDendaRusak.innerText = formatRupiah(dendaKerusakan);

    labelTotalDenda.innerText = formatRupiah(dendaTelat + dendaKerusakan);
}

dendaKerusakanInput.addEventListener('input', calculateDenda);
tglKembaliInput.addEventListener('change', calculateDenda);

// Initial run
calculateDenda();
</script>

    {{-- Wadah hidden untuk input unit_rusak (dipindahkan dari modal ke form) --}}
    <div id="unitRusakHidden" class="hidden"></div>

    {{-- Modal Pemilihan Unit Rusak --}}
    <div id="unitRusakModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" aria-hidden="true" onclick="closeUnitRusakModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-gradient-to-r from-red-600 to-rose-700 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white" id="modal-unit-title">Pilih Unit Rusak</h3>
                        <p class="text-xs text-red-100" id="modal-unit-subtitle">Centang nomor seri yang kembalinya rusak</p>
                    </div>
                    <button type="button" onclick="closeUnitRusakModal()" class="text-white hover:text-gray-200 transition"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div id="unitRusakList" class="grid grid-cols-2 gap-3"></div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeUnitRusakModal()" class="px-5 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAlatId = null;

        function openUnitRusakModal(btn) {
            if (btn.disabled) return;
            currentAlatId = btn.dataset.alat;
            const namaAlat = btn.dataset.nama;
            let units = [];
            try { units = JSON.parse(btn.dataset.units); } catch(e) { units = []; }

            document.getElementById('modal-unit-title').innerText = namaAlat;
            document.getElementById('modal-unit-subtitle').innerText = units.length + ' unit dipinjam — centang yang rusak';

            const container = document.getElementById('unitRusakList');
            container.innerHTML = '';

            if (units.length === 0) {
                container.innerHTML = '<p class="col-span-2 text-center text-gray-400 py-4 text-sm">Tidak ada unit teralokasi.</p>';
            } else {
                units.forEach(unit => {
                    const existing = document.querySelector(`#unitRusakHidden input[name="unit_rusak[${currentAlatId}][]"][value="${unit.id}"]`);
                    const checked = existing ? 'checked' : '';

                    const label = document.createElement('label');
                    label.className = 'inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono cursor-pointer hover:bg-red-50 hover:border-red-200 transition has-[:checked]:bg-red-50 has-[:checked]:border-red-500 has-[:checked]:text-red-700';
                    label.innerHTML = `
                        <input type="checkbox" value="${unit.id}" ${checked}
                            class="rounded text-red-600 focus:ring-red-500 border-gray-300 w-4 h-4"
                            onchange="toggleUnitRusak(this)">
                        <span>${unit.nomor_seri}</span>
                    `;
                    container.appendChild(label);
                });
            }

            document.getElementById('unitRusakModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function toggleUnitRusak(checkbox) {
            const container = document.getElementById('unitRusakHidden');
            const alatId = currentAlatId;

            if (checkbox.checked) {
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = `unit_rusak[${alatId}][]`;
                input.value = checkbox.value;
                input.checked = true;
                input.style.display = 'none';
                container.appendChild(input);
            } else {
                const existing = container.querySelector(`input[name="unit_rusak[${alatId}][]"][value="${checkbox.value}"]`);
                if (existing) existing.remove();
            }

            updateSummary(alatId);
        }

        function updateSummary(alatId) {
            const selected = document.querySelectorAll(`#unitRusakHidden input[name="unit_rusak[${alatId}][]"]:checked`);
            const summary = document.getElementById('summary-' + alatId);
            if (summary) {
                summary.innerHTML = selected.length > 0
                    ? `<b class="text-red-600">${selected.length} unit rusak</b>`
                    : 'Belum ada unit dipilih';
            }
        }

        function closeUnitRusakModal() {
            document.getElementById('unitRusakModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeUnitRusakModal();
        });
    </script>
@endsection
