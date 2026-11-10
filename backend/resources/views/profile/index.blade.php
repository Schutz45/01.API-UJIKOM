@extends(auth()->user()->role === 'peminjam'
    ? 'layouts.peminjam'
    : 'layouts.app')

@section('title', 'Profil Saya - SIPPSD')
@section('header-title', 'Profil Saya')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- PESAN VALIDASI --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 rounded-lg p-4">
                <p class="font-semibold mb-2">
                    Terdapat kesalahan:
                </p>

                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    {{-- HEADER PROFIL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- BANNER --}}
        <div class="relative h-44 sm:h-60 overflow-hidden bg-gradient-to-r from-emerald-600 to-teal-500">

            @if($user->banner_profile)
                <img
                    src="{{ asset('storage/' . $user->banner_profile) }}"
                    alt="Banner Profil"
                    class="absolute inset-0 w-full h-full object-cover object-center">
            @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="bi bi-image text-white/30 text-6xl"></i>
                </div>
            @endif

            {{-- FOTO PROFIL DI ATAS BANNER --}}
            <div class="absolute left-6 top-1/2 -translate-y-1/2 z-20">

                @if($user->foto_profile)
                    <img
                        src="{{ asset('storage/' . $user->foto_profile) }}"
                        alt="Foto Profil"
                        class="w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover border-4 border-white shadow-xl bg-white">
                @else
                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-gray-200 border-4 border-white shadow-xl flex items-center justify-center">
                        <i class="bi bi-person-fill text-gray-500 text-5xl"></i>
                    </div>
                @endif

            </div>

            {{-- Tombol Banner --}}
            <div class="absolute top-4 right-4 flex gap-2">

                <form
                    action="{{ route('profil.banner.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="formBanner">

                    @csrf
                    @method('PUT')

                    <input
                        type="file"
                        name="banner_profile"
                        id="inputBanner"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden">

                    <button
                        type="button"
                        onclick="document.getElementById('inputBanner').click()"
                        class="bg-white/90 hover:bg-white text-gray-700 px-3 py-2 rounded-lg text-sm font-medium shadow transition">

                        <i class="bi bi-camera mr-1"></i>
                        <span class="hidden sm:inline">Ganti Banner</span>
                    </button>

                </form>

                @if($user->banner_profile)
                    <form
                        action="{{ route('profil.banner.destroy') }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus banner?')">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="bg-red-600/90 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm shadow transition">

                            <i class="bi bi-trash"></i>
                        </button>

                    </form>
                @endif

            </div>

        </div>


        {{-- FOTO PROFIL MENUMPUK DI ATAS BANNER --}}
        <div class="relative px-5 sm:px-8 pb-6">

            {{-- Area foto dan identitas --}}
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">

                {{-- IDENTITAS --}}
                <div class="flex-1 min-w-0 pt-1">

                    <h2 class="text-xl font-bold text-gray-800 truncate">
                        {{ $user->name }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        {{ $user->email }}
                    </p>

                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 capitalize">
                        {{ $user->role }}
                    </span>

                </div>

                {{-- TOMBOL FOTO --}}
                <div class="relative z-20 flex flex-wrap gap-2">

                    <form
                        action="{{ route('profil.foto.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="formFoto">

                        @csrf
                        @method('PUT')

                        <input
                            type="file"
                            name="foto_profile"
                            id="inputFoto"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden">

                        <button
                            type="button"
                            onclick="document.getElementById('inputFoto').click()"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">

                            <i class="bi bi-camera mr-1"></i>
                            Ganti Foto
                        </button>

                    </form>

                    @if($user->foto_profile)
                        <form
                            action="{{ route('profil.foto.destroy') }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus foto profil?')">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="px-4 py-2 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-sm font-medium rounded-lg transition">

                                <i class="bi bi-trash mr-1"></i>
                                Hapus
                            </button>

                        </form>
                    @endif

                </div>

            </div>

        </div>



    {{-- FORM INFORMASI AKUN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-7">

        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                Informasi Akun
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Kelola informasi pribadi akun SIPPSD kamu.
            </p>
        </div>

        <form action="{{ route('profil.update') }}" method="POST">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- NAMA --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        maxlength="255"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NOMOR HP --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="{{ old('no_hp', $user->no_hp) }}"
                        maxlength="20"
                        inputmode="numeric"
                        pattern="[0-9]+"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Masukkan nomor HP">

                    @error('no_hp')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Role Akun
                    </label>

                    <input
                        type="text"
                        value="{{ ucfirst($user->role) }}"
                        disabled
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">

                    <p class="text-xs text-gray-400 mt-1">
                        Role akun tidak dapat diubah melalui halaman profil.
                    </p>
                </div>

                {{-- ALAMAT --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        rows="4"
                        maxlength="1000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>

                    @error('alamat')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="flex justify-end mt-6 pt-5 border-t border-gray-100">

                <button
                    type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition shadow-sm">

                    <i class="bi bi-save mr-1"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


{{-- OTOMATIS SUBMIT SAAT GAMBAR DIPILIH --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const inputFoto = document.getElementById('inputFoto');
        const formFoto = document.getElementById('formFoto');

        const inputBanner = document.getElementById('inputBanner');
        const formBanner = document.getElementById('formBanner');

        if (inputFoto && formFoto) {
            inputFoto.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    formFoto.submit();
                }
            });
        }

        if (inputBanner && formBanner) {
            inputBanner.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    formBanner.submit();
                }
            });
        }

    });
</script>

@endsection