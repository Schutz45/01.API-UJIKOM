@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

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

    {{-- Daftar Alat & Kondisi --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Unit yang Dikembalikan
        </h2>
        <p class="text-sm text-gray-500 mb-4">
            Wajib menentukan kondisi (Baik / Rusak) untuk setiap nomor seri sebelum pengembalian dapat diproses.
        </p>
        @foreach ($peminjaman->detailPinjam as $detail)
            <div class="mb-6 last:mb-0">
                <h3 class="font-bold text-gray-700 border-b pb-2 mb-3">{{ $detail->alat->nama_alat }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($detail->unitAlat as $unit)
                        <div class="unit-card border border-gray-200 rounded-lg p-3 flex items-center justify-between bg-gray-50 transition-all duration-200">
                            <span class="font-mono text-sm font-medium">{{ $unit->nomor_seri }}</span>
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

{{-- Form Pengembalian --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-6">Data Pengembalian</h2>

    {{-- Tanggal Kembali --}}
    <div class="mb-5">
        <label for="tgl_kembali" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Kembali</label>
        <input type="date" id="tgl_kembali" name="tgl_kembali" value="{{ old('tgl_kembali', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
    </div>

    {{-- Denda Kerusakan --}}
    <div class="mb-5">
        <label for="denda_kerusakan" class="block text-sm font-semibold text-gray-700 mb-2">
            Nominal Denda Kerusakan
            <span id="denda_wajib_label" class="text-red-600 font-bold hidden">* WAJIB</span>
        </label>
        <input type="number" id="denda_kerusakan" name="denda_kerusakan" value="{{ old('denda_kerusakan') }}" min="0" step="1000" disabled class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400" placeholder="Belum ada unit rusak">
        <p id="denda_catatan" class="text-xs text-gray-500 mt-1">Kolom denda aktif setelah ada unit yang dipilih Rusak.</p>
        @error('denda_kerusakan')
            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
        @enderror
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
                id="btnProsesPengembalian"
                class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <span id="btnText">Proses Pengembalian</span>
                <span id="btnSpinner" class="hidden items-center gap-2">
                    <i class="bi bi-arrow-repeat animate-spin"></i> Memproses...
                </span>
            </button>

        </div>

    </form>
</div>

</div>

<script>
const dendaKerusakanInput = document.getElementById('denda_kerusakan');
const tglKembaliInput = document.getElementById('tgl_kembali');
const radiosKondisi = document.querySelectorAll('input[name^="unit_kondisi["]');
const dendaWajibLabel = document.getElementById('denda_wajib_label');
const dendaCatatan = document.getElementById('denda_catatan');

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
        dendaKerusakanInput.classList.remove('disabled:bg-gray-100', 'disabled:text-gray-400');
    } else {
        dendaKerusakanInput.disabled = true;
        dendaKerusakanInput.required = false;
        dendaKerusakanInput.min = 0;
        dendaKerusakanInput.value = '';
        dendaWajibLabel.classList.add('hidden');
        dendaCatatan.innerText = 'Tidak ada unit rusak, tidak ada denda kerusakan.';
        dendaCatatan.className = 'text-xs text-gray-500 mt-1';
        dendaKerusakanInput.classList.add('disabled:bg-gray-100', 'disabled:text-gray-400');
    }

    calculateDenda();
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

// Loading state saat form disubmit
const formPengembalian = document.querySelector('form[action*="pengembalian"]');
if (formPengembalian) {
    formPengembalian.addEventListener('submit', function() {
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

// Initial run
syncWajibDenda();
</script>
@endsection
