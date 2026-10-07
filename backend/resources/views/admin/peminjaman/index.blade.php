@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')
    
@section('content')
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-b-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                {{-- Judul --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Transaksi Peminjaman
                    </h3>
                </div>

                {{-- Kontrol --}}
                <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">

                    {{-- Filter + Search --}}
                    <form action="{{ route('admin.peminjaman.index') }}"
                        method="GET"
                        class="flex flex-col sm:flex-row sm:flex-wrap gap-2 w-full xl:w-auto">

                        {{-- Filter Urutan --}}
                        <select name="sort"
                            class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg
                            bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="terbaru"
                                {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                                Terbaru
                            </option>

                            <option value="terlama"
                                {{ request('sort') == 'terlama' ? 'selected' : '' }}>
                                Terlama
                            </option>

                            <option value="az"
                                {{ request('sort') == 'az' ? 'selected' : '' }}>
                                A → Z
                            </option>

                            <option value="za"
                                {{ request('sort') == 'za' ? 'selected' : '' }}>
                                Z → A
                            </option>
                        </select>

                        {{-- Search --}}
                        <div class="flex w-full sm:w-80">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama peminjam / status..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                                focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2
                                text-sm font-semibold rounded-r-lg transition whitespace-nowrap">
                                Cari
                            </button>
                        </div>

                        {{-- Reset --}}
                        @if (request('search') || request('status') || request('sort'))
                            <a href="{{ route('admin.peminjaman.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2
                                text-sm rounded-lg flex items-center justify-center transition whitespace-nowrap">
                                Reset
                            </a>
                        @endif

                    </form>

                    {{-- Tambah --}}
                    <a href="{{ route('admin.peminjaman.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                        px-4 py-2 rounded-lg transition whitespace-nowrap text-center
                        flex items-center justify-center">
                        + Tambah Peminjaman
                    </a>

                </div>

            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[950px] w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b whitespace-nowrap">Peminjam</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Alat yang Dipinjam</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Tgl Pinjam / Rencana Kembali</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($peminjamans as $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                                {{ $peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b min-w-[250px]">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($peminjaman->detailPinjam as $detail)
                                        <li class="whitespace-nowrap">
                                            <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">({{ $detail->jumlah }} pcs)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-xs text-gray-500 whitespace-nowrap">
                                <span class="block">Pinjam: {{ $peminjaman->tgl_pinjam }}</span>
                                <span class="block font-semibold">Rencana: {{ $peminjaman->tgl_kembali_plan }}</span>
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{-- Status 'telat' dihitung otomatis oleh accessor getStatusAttribute di Model --}}
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                    @elseif($peminjaman->status == 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($peminjaman->status == 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                    </span>
                                    @if($peminjaman->permintaan_pengembalian)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white animate-pulse shadow-sm">
                                                <i class="bi bi-bell-fill mr-1"></i> MINTA KEMBALI
                                            </span>
                                        </div>
                                    @endif
                            </td>
                            <td class="py-3 px-4 border-b min-w-[170px]">
                                <div class="flex flex-col gap-2">

                                    <!-- Form Ubah Status Cepat -->
                                    @if($peminjaman->status !== 'dikembalikan')
                                        @if($peminjaman->status === 'diajukan')
                                            <button type="button" 
                                                onclick="openAlokasiModal({{ $peminjaman->id }}, {{ json_encode($peminjaman->detailPinjam) }})"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold px-2 py-1.5 rounded transition shadow-sm">
                                                Alokasi & Setujui
                                            </button>
                                        @else
                                            <span class="text-[10px] italic text-gray-400 text-center py-1">
                                                {{ ucfirst($peminjaman->status) }}
                                            </span>
                                        @endif
                                    @endif


                                    <!-- Tombol Kembalikan -->
                                    @if (in_array($peminjaman->status, ['dipinjam', 'telat']))
                                        <a href="{{ route('admin.pengembalian.create', $peminjaman->id) }}"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition text-center whitespace-nowrap">
                                            Kembalikan
                                        </a>
                                    @endif


                                    <!-- Tombol Hapus -->
                                    @if (in_array($peminjaman->status, ['dipinjam', 'telat']))
                                        <button type="button" disabled
                                            class="bg-red-200 text-red-400 px-3 py-1 rounded text-xs font-semibold w-full whitespace-nowrap cursor-not-allowed"
                                            title="Peminjaman yang masih dipinjam atau telat tidak dapat dihapus">
                                            Hapus
                                        </button>
                                    @else
                                        <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition w-full whitespace-nowrap">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $peminjamans->links() }}
        </div>
    </div>

    {{-- Modal Alokasi Unit Alat (Admin) --}}
    <div id="alokasiModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" aria-hidden="true" onclick="closeAlokasiModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form id="alokasiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="dipinjam">

                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white" id="modal-alokasi-title">Pilih Unit Alat untuk Peminjaman</h3>
                            <p class="text-xs text-blue-100">Pilih nomor seri unit yang akan dipinjamkan</p>
                        </div>
                        <button type="button" onclick="closeAlokasiModal()" class="text-white hover:text-gray-200 transition"><i class="bi bi-x-lg"></i></button>
                    </div>

                    <div class="p-6 max-h-[60vh] overflow-y-auto space-y-6" id="alokasiContainer">
                        <!-- JS injects items here -->
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                        <span id="alokasiSelectionCount" class="text-xs text-gray-500 font-medium">0 unit dipilih</span>
                        <div class="flex gap-2">
                            <button type="button" onclick="closeAlokasiModal()" class="px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">Batal</button>
                            <button type="submit" id="btnSubmitAlokasi" class="px-5 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-sm">Setujui & Alokasikan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAlokasiModal(peminjamanId, detailPinjam) {
            const form = document.getElementById('alokasiForm');
            form.action = "{{ url('/admin/peminjaman') }}/" + peminjamanId + "/status";

            const container = document.getElementById('alokasiContainer');
            container.innerHTML = '';

            detailPinjam.forEach(detail => {
                const alat = detail.alat;
                const units = (alat.unit_alat || []).filter(u => u.status === 'tersedia');

                const wrapper = document.createElement('div');
                wrapper.className = 'border border-gray-200 rounded-xl overflow-hidden';

                let unitOptionsHtml = '';
                if (units.length === 0) {
                    unitOptionsHtml = '<p class="text-xs text-red-500 py-2">Tidak ada unit tersedia untuk alat ini.</p>';
                } else {
                    unitOptionsHtml = '<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">';
                    units.forEach(unit => {
                        unitOptionsHtml += `
                            <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 text-xs font-mono transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                <input type="checkbox" name="unit[${detail.alat_id}][]" value="${unit.id}" 
                                    class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5"
                                    onchange="checkAlokasiValidation()">
                                <span>${unit.nomor_seri}</span>
                            </label>
                        `;
                    });
                    unitOptionsHtml += '</div>';
                }

                wrapper.innerHTML = `
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-sm text-gray-800">${alat.nama_alat}</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">Harus dipilih: ${detail.jumlah} unit</span>
                    </div>
                    <div class="p-4">
                        ${unitOptionsHtml}
                    </div>
                `;

                container.appendChild(wrapper);
            });

            checkAlokasiValidation();
            document.getElementById('alokasiModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function checkAlokasiValidation() {
            const checked = document.querySelectorAll('#alokasiContainer input[type="checkbox"]:checked');
            document.getElementById('alokasiSelectionCount').innerText = checked.length + ' unit dipilih';
        }

        function closeAlokasiModal() {
            document.getElementById('alokasiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAlokasiModal();
        });
    </script>
@endsection