@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        <!-- Header tabel -->
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Pengguna Sistem
            </h3>
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 w-full lg:w-auto">

                <!-- Form Search + Filter -->
                <form action="{{ route('admin.user.index') }}" method="GET"
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">

                    <!-- Filter Role -->
                    <select
                        name="role"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="all" {{ request('role', 'all') == 'all' ? 'selected' : '' }}>
                            Semua Role
                        </option>

                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>
                            Petugas
                        </option>

                        <option value="peminjam" {{ request('role') == 'peminjam' ? 'selected' : '' }}>
                            Peminjam
                        </option>
                    </select>

                    <!-- Urutan -->
                    <select
                        name="sort"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                            Terbaru
                        </option>

                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>
                            Terlama
                        </option>

                        <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>
                            A → Z
                        </option>

                        <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>
                            Z → A
                        </option>
                    </select>

                    <!-- Search -->
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, email, role..."
                        class="w-full sm:w-64 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <!-- Tombol Cari -->
                    <button
                        type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap"
                    >
                        Cari
                    </button>

                    <!-- Reset -->
                    @if(request('search') || request('role') || request('sort'))
                        <a
                            href="{{ route('admin.user.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center justify-center transition whitespace-nowrap"
                        >
                            Reset
                        </a>
                    @endif

                </form>

                <!-- Tombol Tambah User -->
                <a
                    href="{{ route('admin.user.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap text-center"
                >
                    + Tambah User
                </a>

            </div>
        </div>

        <!-- Tabel -->
        <div class="w-full overflow-x-auto">

            <table class="min-w-[800px] w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b whitespace-nowrap">Nama</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Email</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">No. HP</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Hak Akses</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Status Akun</th>
                        <th class="py-3 px-4 border-b whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($users as $user)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center gap-3">

                                    @if($user->foto_profile)
                                        <img
                                            src="{{ asset('storage/' . $user->foto_profile) }}"
                                            alt="Foto {{ $user->name }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200"
                                        >
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <button
                                        type="button"
                                        onclick="showAdminUserProfile(this)"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email ?? '-' }}"
                                        data-no-hp="{{ $user->no_hp ?? '-' }}"
                                        data-alamat="{{ $user->alamat ?? '-' }}"
                                        data-foto="{{ $user->foto_profile ? asset('storage/' . $user->foto_profile) : '' }}"
                                        data-role="{{ ucfirst($user->role) }}"
                                        data-status="{{ $user->isAktif() ? 'Aktif' : 'Nonaktif' }}"
                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline text-left transition">
                                        {{ $user->name }}
                                    </button>

                                </div>
                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->email }}
                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->no_hp ?? '-' }}
                            </td>


                            <td class="py-3 px-4 border-b">

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($user->role == 'admin')
                                        bg-purple-100 text-purple-800
                                    @elseif($user->role == 'petugas')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-green-100 text-green-800
                                    @endif">

                                    {{ ucfirst($user->role) }}

                                </span>

                            </td>

                            {{-- STATUS AKUN --}}
                            <td class="py-3 px-4 border-b">
                                @if($user->isAktif())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 border-b">

                                <div class="flex items-center gap-2 whitespace-nowrap">

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.user.edit', $user->id) }}"
                                       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.user.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus User ini? {{ $user->id == auth()->id() ? 'Ini adalah akun Anda sendiri!' : '' }}')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition {{ $user->id == auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                            Hapus
                                        </button>

                                    </form>

                                    <!-- Tombol Aktifkan / Nonaktifkan -->
                                    @if($user->id == auth()->id())
                                        <span class="text-[10px] italic text-gray-400 px-2 py-1.5">
                                            Akun Anda
                                        </span>
                                    @elseif($user->isAktif())
                                        <form action="{{ route('admin.user.status', $user->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menonaktifkan akun {{ $user->name }}? Akun ini tidak akan dapat login sementara.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.user.status', $user->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Aktifkan kembali akun {{ $user->name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                Belum ada pengguna terdaftar.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
        </div>

    </div>

    {{-- Modal Profil Pengguna --}}
<div id="adminUserProfileModal"
    class="fixed inset-0 z-[9999] hidden overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            aria-hidden="true"
            onclick="closeAdminUserProfile()">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
            aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">
                        Profil Pengguna
                    </h3>

                    <button type="button"
                        onclick="closeAdminUserProfile()"
                        class="text-white hover:text-gray-200 transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Isi Profil --}}
            <div class="px-6 py-6 bg-white">

                <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">

                    {{-- Foto Profil --}}
                    <div id="admin-modal-foto-fallback"
                        class="hidden w-24 h-24 rounded-2xl bg-gray-200 text-gray-600
                        flex items-center justify-center text-3xl font-bold border-4
                        border-gray-100 shadow-sm shrink-0">
                    </div>

                    <img id="admin-modal-foto"
                        src=""
                        alt="Foto Profil"
                        class="w-24 h-24 rounded-2xl object-cover border-4 border-gray-100 shadow-sm shrink-0">

                    <div class="flex-1 text-center sm:text-left">
                        <h4 id="admin-modal-name"
                            class="text-xl font-bold text-gray-900">
                        </h4>

                        <p id="admin-modal-role"
                            class="text-sm font-medium text-blue-600">
                        </p>

                        <span id="admin-modal-status"
                            class="inline-block mt-2 px-2.5 py-1 text-xs font-semibold rounded-full">
                        </span>
                    </div>

                </div>

                <hr class="my-6 border-gray-100">

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
                            <p id="admin-modal-email"
                                class="text-gray-700 font-medium break-all">
                            </p>
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
                            <p id="admin-modal-no-hp"
                                class="text-gray-700 font-medium">
                            </p>
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
                            <p id="admin-modal-alamat"
                                class="text-gray-700 font-medium">
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button"
                    onclick="closeAdminUserProfile()"
                    class="px-5 py-2 text-sm font-bold text-gray-700 bg-white
                    border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>


<script>
    function showAdminUserProfile(button) {
        const data = button.dataset;

        const name = data.name || 'User';
        const foto = data.foto || '';

        // Isi informasi profil
        document.getElementById('admin-modal-name').textContent = name;
        document.getElementById('admin-modal-role').textContent = data.role || '-';
        document.getElementById('admin-modal-email').textContent = data.email || '-';
        document.getElementById('admin-modal-no-hp').textContent = data.noHp || '-';
        document.getElementById('admin-modal-alamat').textContent = data.alamat || '-';

        // Status akun
        const status = document.getElementById('admin-modal-status');
        status.textContent = data.status || '-';

        status.className = 'inline-block mt-2 px-2.5 py-1 text-xs font-semibold rounded-full';

        if (data.status === 'Aktif') {
            status.classList.add('bg-emerald-100', 'text-emerald-800');
        } else {
            status.classList.add('bg-gray-200', 'text-gray-600');
        }

        // Foto profil dan fallback inisial
        const image = document.getElementById('admin-modal-foto');
        const fallback = document.getElementById('admin-modal-foto-fallback');

        fallback.textContent = name.trim().charAt(0).toUpperCase() || 'U';

        if (foto) {
            image.src = foto;
            image.classList.remove('hidden');
            fallback.classList.add('hidden');

            image.onerror = function () {
                image.classList.add('hidden');
                fallback.classList.remove('hidden');
            };
        } else {
            image.removeAttribute('src');
            image.classList.add('hidden');
            fallback.classList.remove('hidden');
        }

        // Buka modal
        document.getElementById('adminUserProfileModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAdminUserProfile() {
        document.getElementById('adminUserProfileModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Tutup modal menggunakan tombol Escape
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAdminUserProfile();
        }
    });
</script>


@endsection