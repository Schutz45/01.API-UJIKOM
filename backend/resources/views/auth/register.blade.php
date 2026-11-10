<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIPPSD - Sistem Informasi Peminjaman Peralatan Sekolah Digital</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-8">

    <!-- Card Container -->
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-3xl mx-4">

        <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Register Akun
        </h3>

        <!-- Alert Error Validasi -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                <ul class="list-disc pl-5 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Form Dua Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Kolom Kiri -->
                <div class="space-y-4">

                    <!-- Input Nama -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Minimal 6 karakter">
                    </div>

                    <!-- Input Nomor HP -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Nomor HP (Opsional)
                        </label>
                        <input
                            type="text"
                            name="no_hp"
                            value="{{ old('no_hp') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nomor HP">
                    </div>

                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">

                    <!-- Input Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan email">
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Konfirmasi Password
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ulangi password">
                    </div>

                    <!-- Input Foto Profil -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Foto Profil (Opsional)
                        </label>
                        <input
                            type="file"
                            name="foto_profile"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                        </p>
                    </div>

                </div>

            </div>

            <!-- Input Alamat (Full Width) -->
            <div class="mt-4 mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Alamat (Opsional)
                </label>
                <textarea
                    name="alamat"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
            </div>

            <!-- Tombol Submit -->
            <button
                type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Daftar
            </button>

            <!-- Link Login -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?

                    <a href="{{ route('login') }}"
                        class="text-blue-600 hover:text-blue-700 font-semibold">
                        Login
                    </a>
                </p>
            </div>

        </form>
    </div>

</body>
</html>