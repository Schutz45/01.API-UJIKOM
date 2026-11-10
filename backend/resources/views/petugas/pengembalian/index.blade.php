@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan Pengembalian Alat')

@section('content')

    {{-- Container --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Peminjaman Aktif
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Daftar alat yang masih dipinjam atau sudah terlambat dikembalikan.
            </p>

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
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b whitespace-nowrap">
                            Batas Kembali
                        </th>

                        <th class="py-3 px-4 border-b min-w-[280px]">
                            Detail Alat
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[190px]">
                            Status
                        </th>

                        <th class="py-3 px-4 border-b text-center whitespace-nowrap min-w-[190px]">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjamans as $item)

                        <tr class="hover:bg-gray-50 transition align-top">


                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                                @php
                                    $user = $item->user;
                                    $namaUser = $user->name ?? 'User';
                                    $fotoUser = $user && $user->foto_profile
                                        ? asset('storage/' . $user->foto_profile)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($namaUser);
                                @endphp

                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $fotoUser }}"
                                        alt="Foto {{ $namaUser }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0"
                                    >

                                    <button
                                        type="button"
                                        data-name="{{ $namaUser }}"
                                        data-email="{{ $user->email ?? '-' }}"
                                        data-no-hp="{{ $user->no_hp ?? '-' }}"
                                        data-alamat="{{ $user->alamat ?? '-' }}"
                                        data-foto="{{ $fotoUser }}"
                                        onclick="showUserModalFromButton(this)"
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-left font-bold transition"
                                    >
                                        {{ $namaUser }}
                                    </button>
                                </div>
                            </td>



                            {{-- Tanggal Pinjam --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_pinjam }}
                            </td>


                            {{-- Batas Kembali --}}
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

                                        {{-- Tampilkan nomor seri unit yang dialokasikan --}}
                                        @if($detail->unitAlat->isNotEmpty())
                                            <li class="ml-4">
                                                <span class="flex flex-wrap gap-1 mt-0.5">
                                                    @foreach($detail->unitAlat as $unit)
                                                        @php
                                                            $kondisiMasuk = $unit->pivot->kondisi_masuk;
                                                            $badgeClass = $kondisiMasuk === 'rusak'
                                                                ? 'bg-red-100 text-red-700 border-red-200'
                                                                : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                        @endphp
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium border {{ $badgeClass }}">
                                                            {{ $unit->nomor_seri }}
                                                            @if($kondisiMasuk === 'rusak')
                                                                <i class="bi bi-x-circle-fill ml-0.5"></i>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </span>
                                            </li>
                                        @endif

                                    @endforeach

                                </ul>

                            </td>


                            {{-- Status --}}
                            <td class="py-3 px-4 border-b text-center whitespace-nowrap">

                                @if($item->status === 'dipinjam')

                                    @if($item->permintaan_pengembalian)

                                        <span class="inline-block text-xs font-semibold
                                                     text-amber-600 bg-amber-50
                                                     px-2.5 py-1 rounded">
                                            Menunggu Pengembalian
                                        </span>

                                    @else

                                        <span class="inline-block text-xs font-semibold
                                                     text-blue-600 bg-blue-50
                                                     px-2.5 py-1 rounded">
                                            Dipinjam
                                        </span>

                                    @endif

                                @elseif($item->status === 'telat')

                                    @if($item->permintaan_pengembalian)

                                        <span class="inline-block text-xs font-semibold
                                                     text-amber-600 bg-amber-50
                                                     px-2.5 py-1 rounded">
                                            Menunggu Pengembalian
                                        </span>

                                    @else

                                        <span class="inline-block text-xs font-semibold
                                                     text-red-600 bg-red-50
                                                     px-2.5 py-1 rounded">
                                            Telat
                                        </span>

                                    @endif

                                @endif

                            </td>


                            {{-- Aksi --}}
                            <td class="py-3 px-4 border-b text-center min-w-[190px]">

                                <div class="flex justify-center items-center whitespace-nowrap">

                                    @if($item->permintaan_pengembalian)

                                        <a
                                            href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600
                                                   text-white px-3 py-1.5 rounded
                                                   text-xs font-semibold
                                                   transition shadow-sm"
                                        >
                                            Menunggu Pengembalian
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                            class="bg-emerald-600 hover:bg-emerald-700
                                                   text-white px-3 py-1.5 rounded
                                                   text-xs font-semibold
                                                   transition shadow-sm"
                                        >
                                            Kembalikan
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-6 text-center text-gray-500"
                            >
                                Tidak ada peminjaman yang sedang aktif.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Modal Profil Peminjam --}}
    <div id="userProfileModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                aria-hidden="true" onclick="closeUserModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white" id="modal-title">Profil Peminjam</h3>
                        <button type="button" onclick="closeUserModal()" class="text-white hover:text-gray-200">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 bg-white">
                    <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">
                        <img id="modal-foto" src="" alt="Foto profil"
                            class="w-24 h-24 rounded-2xl object-cover border-4 border-gray-100 shadow-sm">

                        <div class="flex-1 text-center sm:text-left">
                            <h4 id="modal-name" class="text-xl font-bold text-gray-900"></h4>
                            <p class="text-sm font-medium text-blue-600">Peminjam</p>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-100">

                    <div class="grid grid-cols-1 gap-5">
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Email</p>
                                <p id="modal-email" class="text-gray-700 font-medium"></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">No. Telepon</p>
                                <p id="modal-no-hp" class="text-gray-700 font-medium"></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Alamat</p>
                                <p id="modal-alamat" class="text-gray-700 font-medium"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeUserModal()"
                            class="px-5 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>

    function showUserModal(user) {
        document.getElementById('modal-name').textContent = user.name;
        document.getElementById('modal-email').textContent = user.email;
        document.getElementById('modal-no-hp').textContent = user.no_hp;
        document.getElementById('modal-alamat').textContent = user.alamat;
        document.getElementById('modal-foto').src = user.foto;

        document.getElementById('userProfileModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function showUserModalFromButton(button) {
        const user = {
            name: button.dataset.name || 'User',
            email: button.dataset.email || '-',
            no_hp: button.dataset.noHp || '-',
            alamat: button.dataset.alamat || '-',
            foto: button.dataset.foto || ''
        };

        showUserModal(user);
    }

    function closeUserModal() {
        document.getElementById('userProfileModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

</script>

@endsection