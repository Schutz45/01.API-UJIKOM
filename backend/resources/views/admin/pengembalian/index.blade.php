@extends('layouts.app')

@section('title', 'Riwayat Pengembalian - Panel Admin')
@section('header-title', 'Riwayat Pengembalian Alat')

@section('content')

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

            {{-- Judul --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Riwayat Pengembalian
                </h3>
            </div>

            {{-- Kontrol --}}
            <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">

                {{-- Filter + Search --}}
                <form
                    action="{{ route('admin.pengembalian.index') }}"
                    method="GET"
                    class="flex flex-col sm:flex-row sm:flex-wrap gap-2 w-full xl:w-auto"
                >

                    {{-- Filter Kondisi --}}
                    <select
                        name="kondisi"
                        class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">
                            Semua Kondisi
                        </option>

                        <option value="baik"
                            {{ request('kondisi') == 'baik' ? 'selected' : '' }}>
                            Baik
                        </option>

                        <option value="rusak"
                            {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>
                            Rusak
                        </option>
                    </select>

                    {{-- Filter Urutan --}}
                    <select
                        name="sort"
                        class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg
                        bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
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
                            placeholder="Cari nama peminjam / kondisi..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                            focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                        <button
                            type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2
                            text-sm font-semibold rounded-r-lg transition whitespace-nowrap"
                        >
                            Cari
                        </button>
                    </div>

                    {{-- Reset --}}
                    @if(request('search') || request('kondisi') || request('sort'))
                        <a
                            href="{{ route('admin.pengembalian.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2
                            text-sm rounded-lg flex items-center justify-center
                            transition whitespace-nowrap"
                        >
                            Reset
                        </a>
                    @endif

                </form>

            </div>

        </div>

    </div>

    {{-- Tabel --}}
    <div class="w-full overflow-x-auto">

        <table class="min-w-[1100px] w-full text-left border-collapse">

            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Peminjam
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Alat
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Rencana Kembali
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Tanggal Kembali
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Kondisi
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Denda
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Petugas
                    </th>

                    <th class="py-3 px-4 border-b text-center whitespace-nowrap">
                        Aksi
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($pengembalians as $pengembalian)

                    <tr class="hover:bg-gray-50 transition align-top">

                        {{-- Peminjam --}}
                        <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                            <div class="flex items-center gap-3">

                                {{-- Foto Profil --}}
                                @if($pengembalian->peminjaman?->user?->foto_profile)
                                    <img
                                        src="{{ asset('storage/' . $pengembalian->peminjaman->user->foto_profile) }}"
                                        alt="Foto {{ $pengembalian->peminjaman->user->name }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0"
                                    >
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-semibold shrink-0">
                                        {{ strtoupper(substr($pengembalian->peminjaman?->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif

                                {{-- Nama Peminjam --}}
                                <button
                                    type="button"
                                    onclick="showUserModal({
                                        name: {{ Js::from($pengembalian->peminjaman?->user?->name ?? 'User Dihapus') }},
                                        email: {{ Js::from($pengembalian->peminjaman?->user?->email ?? '-') }},
                                        no_hp: {{ Js::from($pengembalian->peminjaman?->user?->no_hp ?? '-') }},
                                        alamat: {{ Js::from($pengembalian->peminjaman?->user?->alamat ?? '-') }},
                                        foto: {{ Js::from($pengembalian->peminjaman?->user?->foto_profile ? asset('storage/' . $pengembalian->peminjaman->user->foto_profile) : 'https://ui-avatars.com/api/?name=User') }}
                                    })"
                                    class="text-blue-600 hover:text-blue-800 hover:underline text-left font-bold transition"
                                >
                                    {{ $pengembalian->peminjaman?->user?->name ?? 'User Dihapus' }}
                                </button>

                            </div>
                        </td>

                        {{-- Alat --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            <ul class="list-disc list-inside space-y-1">

                                @foreach($pengembalian->peminjaman->detailPinjam as $detail)

                                    <li class="whitespace-nowrap">
                                        <span class="font-semibold">
                                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                        </span>

                                        <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">
                                            ({{ $detail->jumlah - $detail->unitAlat->where('pivot.kondisi_masuk', 'rusak')->count() }} Baik, {{ $detail->unitAlat->where('pivot.kondisi_masuk', 'rusak')->count() }} Rusak)
                                        </span>
                                    </li>

                                @endforeach

                            </ul>

                        </td>

                        {{-- Rencana kembali --}}
                        <td class="py-3 px-4 border-b text-xs text-gray-500">
                            {{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d-m-Y') }}
                        </td>

                        {{-- Tanggal kembali --}}
                        <td class="py-3 px-4 border-b text-xs whitespace-nowrap">
                            {{ $pengembalian->tgl_kembali?->format('d-m-Y') }}
                        </td>

                        {{-- Kondisi --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            @if($pengembalian->kondisi_kembali === 'baik')

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    Baik
                                </span>

                            @elseif($pengembalian->kondisi_kembali === 'rusak')

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rusak
                                </span>

                            @else

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($pengembalian->kondisi_kembali) }}
                                </span>

                            @endif

                        </td>

                        {{-- Denda --}}
                        <td class="py-3 px-4 border-b font-semibold whitespace-nowrap">
                            Rp{{ number_format($pengembalian->denda, 0, ',', '.') }}
                        </td>

                        {{-- Petugas --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            {{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="py-3 px-4 border-b text-center min-w-[160px]">
                            <div class="flex justify-center items-center gap-2 whitespace-nowrap">

                            {{-- Detail --}}
                                <a href="{{ route('admin.pengembalian.show', $pengembalian->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition" >
                                    <i class="bi bi-eye-fill mr-1"></i> Detail
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="py-8 text-center text-gray-500"
                        >
                            Belum ada riwayat pengembalian.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $pengembalians->links() }}
    </div>

</div>

{{-- Modal Profil Peminjam --}}
<div id="userProfileModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
             aria-hidden="true"
             onclick="closeUserModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">
                        Profil Peminjam
                    </h3>

                    <button type="button"
                            onclick="closeUserModal()"
                            class="text-white hover:text-gray-200 transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Isi Profil --}}
            <div class="px-6 py-6 bg-white">

                <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">

                    <img id="modal-foto"
                         src=""
                         alt="Profile"
                         class="w-24 h-24 rounded-2xl object-cover border-4 border-gray-100 shadow-sm">

                    <div class="flex-1 text-center sm:text-left">
                        <h4 id="modal-name"
                            class="text-xl font-bold text-gray-900"></h4>

                        <p class="text-sm font-medium text-blue-600">
                            Peminjam
                        </p>
                    </div>

                </div>

                <hr class="my-6 border-gray-100">

                {{-- Informasi --}}
                <div class="grid grid-cols-1 gap-5">

                    {{-- Email --}}
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                            <i class="bi bi-envelope"></i>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">
                                Email
                            </p>
                            <p id="modal-email" class="text-gray-700 font-medium"></p>
                        </div>
                    </div>

                    {{-- Nomor HP --}}
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">
                                No. Telepon
                            </p>
                            <p id="modal-no-hp" class="text-gray-700 font-medium"></p>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">
                                Alamat
                            </p>
                            <p id="modal-alamat" class="text-gray-700 font-medium"></p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button"
                        onclick="closeUserModal()"
                        class="px-5 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>




<script>
    function showUserModal(user) {
        document.getElementById('modal-name').innerText = user.name || 'User Dihapus';
        document.getElementById('modal-email').innerText = user.email || '-';
        document.getElementById('modal-no-hp').innerText = user.no_hp || '-';
        document.getElementById('modal-alamat').innerText = user.alamat || '-';

        const foto = document.getElementById('modal-foto');

        foto.onerror = function () {
            foto.onerror = null;
            foto.src = 'https://ui-avatars.com/api/?name=User';
        };

        foto.src = user.foto || 'https://ui-avatars.com/api/?name=User';

        document.getElementById('userProfileModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUserModal() {
        document.getElementById('userProfileModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeUserModal();
        }
    });
</script>




@endsection