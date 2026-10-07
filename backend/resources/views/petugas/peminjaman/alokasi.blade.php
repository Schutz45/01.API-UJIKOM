@extends('layouts.app')

@section('title', 'Alokasi Unit Alat - Dashboard Petugas')
@section('header-title', 'Alokasi Nomor Seri Unit')

@section('content')

    {{-- Alert --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <a href="{{ route('petugas.peminjaman.index') }}" class="text-sm text-gray-500 hover:text-gray-800 inline-flex items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke daftar pengajuan
            </a>
            <h3 class="text-lg font-bold text-gray-800">Pilih Unit untuk Peminjaman #{{ $peminjaman->id }}</h3>
            <p class="text-sm text-gray-500 mt-1">
                Peminjam: <span class="font-semibold text-gray-700">{{ $peminjaman->user->name ?? '-' }}</span> &middot;
                Tanggal Pinjam: <span class="font-semibold text-gray-700">{{ $peminjaman->tgl_pinjam }}</span>
            </p>
        </div>

        {{-- Form --}}
        <form action="{{ route('petugas.peminjaman.setujui', $peminjaman->id) }}" method="POST">
            @csrf

            <div class="p-6 space-y-6">

                @foreach($peminjaman->detailPinjam as $detail)
                    @php
                        $unitsTersedia = $detail->alat->unitAlat->where('status', 'tersedia');
                    @endphp

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        {{-- Header alat --}}
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $detail->alat->kategori->nama_kategori ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Jumlah Dipinjam</p>
                                <p class="text-lg font-extrabold text-blue-700">{{ $detail->jumlah }}</p>
                            </div>
                        </div>

                        {{-- Grid pilihan unit --}}
                        <div class="p-5">
                            @if($unitsTersedia->count() < $detail->jumlah)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                                    <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                                    Unit tersedia tidak mencukupi. Tersedia: <b>{{ $unitsTersedia->count() }}</b>, dibutuhkan: <b>{{ $detail->jumlah }}</b>.
                                </div>
                            @else
                                <p class="text-xs text-gray-500 mb-3">
                                    Pilih <b>{{ $detail->jumlah }}</b> unit dari {{ $unitsTersedia->count() }} unit yang tersedia:
                                </p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($unitsTersedia as $unit)
                                        <label class="relative flex items-center gap-2.5 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-300">
                                            <input type="checkbox" name="unit[{{ $detail->alat_id }}][]" value="{{ $unit->id }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 shrink-0">
                                            <div class="min-w-0">
                                                <p class="font-mono text-sm font-bold text-gray-800 truncate">{{ $unit->nomor_seri }}</p>
                                                <p class="text-[10px] text-green-600 font-medium uppercase">Tersedia</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                @endforeach

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <p class="text-xs text-gray-500" id="selectionInfo">Belum ada unit dipilih.</p>
                <button type="submit" id="submitBtn"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                    <i class="bi bi-check-lg mr-1"></i> Setujui & Alokasikan
                </button>
            </div>
        </form>

    </div>

    <script>
        // Hitung total unit yang dipilih dan tampilkan di info
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="unit["]');
        const info = document.getElementById('selectionInfo');
        const submitBtn = document.getElementById('submitBtn');

        function updateInfo() {
            const checked = document.querySelectorAll('input[type="checkbox"][name^="unit["]:checked');
            const total = checked.length;
            info.innerText = total + ' unit dipilih dari total yang dibutuhkan.';
            submitBtn.disabled = total === 0;
            submitBtn.classList.toggle('opacity-50', total === 0);
            submitBtn.classList.toggle('cursor-not-allowed', total === 0);
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateInfo));
        updateInfo();
    </script>

@endsection
