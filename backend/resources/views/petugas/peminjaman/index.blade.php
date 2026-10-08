@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Dashboard Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')

    {{-- Container --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50
                    flex flex-col lg:flex-row
                    lg:items-center lg:justify-between
                    gap-4">

            {{-- Judul --}}
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-800">
                    Menunggu Verifikasi Persetujuan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Daftar pengajuan peminjaman yang menunggu persetujuan petugas.
                </p>
            </div>


            {{-- Form Search --}}
            <form
                action="{{ route('petugas.peminjaman.index') }}"
                method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto"
            >

                <div class="flex w-full sm:w-80">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peminjam..."
                        class="w-full px-3 py-2 text-sm border border-gray-300
                               rounded-lg sm:rounded-r-none
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <button
                        type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white
                               px-4 py-2 text-sm font-semibold
                               rounded-lg sm:rounded-l-none
                               transition whitespace-nowrap"
                    >
                        Cari
                    </button>
                </div>

                @if(request('search'))
                    <a
                        href="{{ route('petugas.peminjaman.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700
                               px-3 py-2 text-sm rounded-lg
                               flex items-center justify-center
                               transition whitespace-nowrap"
                    >
                        Reset
                    </a>
                @endif

            </form>

        </div>


        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[950px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Rencana Kembali
                        </th>

                        <th class="py-3 px-4 border-b min-w-[280px]">
                            Detail Alat
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[170px]">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjamans as $item)

                        <tr class="hover:bg-gray-50 transition align-top">

                            <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                                <button type="button" 
                                    onclick="showUserModal({
                                        name: '{{ addslashes($item->user->name ?? 'User') }}',
                                        email: '{{ addslashes($item->user->email ?? '-') }}',
                                        no_hp: '{{ addslashes($item->user->no_hp ?? '-') }}',
                                        alamat: '{{ addslashes($item->user->alamat ?? '-') }}',
                                        foto: '{{ $item->user->foto_profile ? asset('storage/' . $item->user->foto_profile) : 'https://ui-avatars.com/api/?name=' . urlencode($item->user->name ?? 'User') }}'
                                    })"
                                    class="text-blue-600 hover:text-blue-800 hover:underline text-left font-bold transition">
                                    {{ $item->user->name ?? '-' }}
                                </button>
                            </td>


                            {{-- Tanggal Pinjam --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_pinjam }}
                            </td>


                            {{-- Rencana Kembali --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_kembali_plan }}
                            </td>


                            {{-- Detail Alat --}}
                            <td class="py-3 px-4 border-b min-w-[280px]">

                                <ul class="list-disc list-inside space-y-1 text-xs">

                                    @foreach($item->detailPinjam as $detail)

                                        <li class="whitespace-nowrap">
                                            <span class="font-semibold">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>

                                            (Jumlah: {{ $detail->jumlah }})
                                        </li>

                                    @endforeach

                                </ul>

                            </td>


                            {{-- Aksi --}}
                            <td class="py-3 px-4 border-b text-center min-w-[170px]">

                                @if($item->status == 'diajukan')

                                    <div class="flex justify-center items-center gap-2 whitespace-nowrap">

                                        {{-- Tombol Setujui (ke halaman alokasi unit) --}}
                                        <a
                                            href="{{ route('petugas.peminjaman.alokasi', $item->id) }}"
                                            class="bg-emerald-600 hover:bg-emerald-700
                                                   text-white px-3 py-1.5 rounded
                                                   text-xs font-semibold
                                                   transition shadow-sm inline-flex items-center gap-1"
                                        >
                                            <i class="bi bi-check-lg"></i>
                                            Setujui
                                        </a>

                                        {{-- Tombol Tolak --}}
                                        <form
                                            action="{{ route('petugas.peminjaman.tolak', $item->id) }}"
                                            method="POST"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Yakin ingin menolak peminjaman alat ini?')"
                                                class="bg-red-500 hover:bg-red-600
                                                       text-white px-3 py-1.5 rounded
                                                       text-xs font-semibold
                                                       transition shadow-sm"
                                            >
                                                Tolak
                                            </button>
                                        </form>

                                    </div>

                                @else

                                    <span class="text-xs font-semibold 
                                                 @if($item->status === 'telat') text-red-600 bg-red-50 @else text-blue-600 bg-blue-50 @endif
                                                 px-2.5 py-1 rounded">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="py-6 text-center text-gray-500"
                            >
                                Tidak ada pengajuan peminjaman baru.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Modal Profil Peminjam --}}
<div id="userProfileModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeUserModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white" id="modal-title">Profil Peminjam</h3>
                    <button type="button" onclick="closeUserModal()" class="text-white hover:text-gray-200 transition"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="px-6 py-6 bg-white">
                <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">
                    <img id="modal-foto" src="" alt="Profile" class="w-24 h-24 rounded-2xl object-cover border-4 border-gray-100 shadow-sm">
                    <div class="flex-1 text-center sm:text-left">
                        <h4 id="modal-name" class="text-xl font-bold text-gray-900"></h4>
                        <p class="text-sm font-medium text-blue-600">Peminjam</p>
                    </div>
                </div>
                <hr class="my-6 border-gray-100">
                <div class="grid grid-cols-1 gap-5">
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0"><i class="bi bi-envelope"></i></div>
                        <div><p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Email</p><p id="modal-email" class="text-gray-700 font-medium"></p></div>
                    </div>
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0"><i class="bi bi-telephone"></i></div>
                        <div><p class="text-xs text-gray-400 font-medium uppercase tracking-wider">No. Telepon</p><p id="modal-no-hp" class="text-gray-700 font-medium"></p></div>
                    </div>
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0"><i class="bi bi-geo-alt"></i></div>
                        <div><p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Alamat</p><p id="modal-alamat" class="text-gray-700 font-medium"></p></div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeUserModal()" class="px-5 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showUserModal(user) {
        document.getElementById('modal-name').innerText = user.name;
        document.getElementById('modal-email').innerText = user.email;
        document.getElementById('modal-no-hp').innerText = user.no_hp;
        document.getElementById('modal-alamat').innerText = user.alamat;
        document.getElementById('modal-foto').src = user.foto;
        document.getElementById('userProfileModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeUserModal() {
        document.getElementById('userProfileModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
