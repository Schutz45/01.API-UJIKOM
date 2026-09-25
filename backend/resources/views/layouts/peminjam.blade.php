<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SIPPSD - Sistem Informasi Peminjaman Peralatan Sekolah Digital')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')
</head>

<body class="bg-slate-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- =====================================================
             SIDEBAR
        ====================================================== --}}
        <aside
            class="relative w-64 bg-slate-950 text-white
                   flex flex-col hidden md:flex shrink-0 overflow-hidden">

            {{-- =================================================
                 LOGO
            ================================================== --}}
            <div class="px-5 py-5 border-b border-slate-800 relative z-20">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-gradient-to-br from-red-500 to-purple-600
                               flex items-center justify-center
                               shadow-lg shrink-0">

                        <i class="bi bi-fire text-xl"></i>

                    </div>

                    <div class="min-w-0">

                        <h1 class="text-lg font-bold tracking-wide">
                            SIPPSD
                        </h1>

                        <p class="text-[9px] leading-tight text-slate-400">
                            Sistem Informasi Peminjaman
                            Peralatan Sekolah Digital
                        </p>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 MENU
            ================================================== --}}
            <nav class=" relative z-20 flex-1 px-4 py-5">

                <p
                    class="px-3 mb-3 text-[11px]
                           font-semibold uppercase tracking-wider
                           text-slate-500">

                    Menu Utama

                </p>


                {{-- Katalog --}}
                <a
                    href="{{ route('peminjam.katalog') }}"
                    class="flex items-center gap-3 px-3 py-3 mb-2 rounded-xl
                           transition
                           {{ request()->routeIs('peminjam.katalog')
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/30'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                    <i class="bi bi-grid-1x2-fill text-lg"></i>

                    <span class="text-sm font-medium">
                        Katalog Alat
                    </span>

                </a>


                {{-- Riwayat --}}
                <a
                    href="{{ route('peminjam.riwayat') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl
                           transition
                           {{ request()->routeIs('peminjam.riwayat')
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/30'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                    <i class="bi bi-clock-history text-lg"></i>

                    <span class="text-sm font-medium">
                        Riwayat Peminjaman
                    </span>

                </a>

            </nav>


            {{-- =====================================================
                DEKORASI MEGUMIN SIDEBAR
            ====================================================== --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">

                <img
                    src="{{ asset('storage/images/megumin-sidebar.png') }}"
                    alt=""
                    class="absolute inset-0
                        w-full h-full
                        object-cover 
                        object-[10%_center]
                        opacity-45">
                
                {{-- Lapisan gelap agar menu tetap terbaca --}}
                <div class="absolute inset-0 bg-slate-950/45"></div>

            </div>


            {{-- =================================================
                 BAGIAN BAWAH
            ================================================== --}}
            <div
                class="relative z-20 px-4 pb-4
                       bg-gradient-to-t from-slate-950
                       via-slate-950/95 to-transparent pt-8">

                {{-- Profil --}}
                <div
                    class="border-t border-slate-800
                           pt-4 mb-3">

                    <div class="flex items-center gap-3 px-2">

                        <div
                            class="w-9 h-9 rounded-full
                                   bg-indigo-500
                                   flex items-center justify-center
                                   shrink-0">

                            <i class="bi bi-person-fill"></i>

                        </div>

                        <div class="min-w-0">

                            <p
                                class="text-sm font-semibold
                                       text-white truncate">

                                {{ auth()->user()->name }}

                            </p>

                            <p
                                class="text-xs text-slate-500
                                       capitalize">

                                {{ auth()->user()->role }}

                            </p>

                        </div>

                    </div>

                </div>


                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3
                               px-3 py-3 rounded-xl
                               text-slate-400
                               hover:bg-red-500/10
                               hover:text-red-400
                               transition">

                        <i class="bi bi-box-arrow-right text-lg"></i>

                        <span class="text-sm font-medium">
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        {{-- =====================================================
             AREA UTAMA
        ====================================================== --}}
        <div class="flex-1 flex flex-col overflow-hidden">


            {{-- =================================================
                 HEADER
            ================================================== --}}
            <header
                class="h-16 bg-white
                       border-b border-slate-200
                       flex items-center justify-between
                       px-6 shrink-0">

                {{-- Judul --}}
                <div>

                    <h1 class="text-lg font-semibold text-slate-800">

                        @yield('header-title', 'Katalog Alat')

                    </h1>

                    <p class="text-xs text-slate-400 hidden sm:block">
                        Sistem Peminjaman Peralatan Sekolah Digital
                    </p>

                </div>


                {{-- User Header --}}
                <div class="flex items-center gap-4">

                    {{-- Notifikasi --}}
                    <button
                        type="button"
                        class="w-9 h-9 rounded-full
                               flex items-center justify-center
                               text-slate-500
                               hover:bg-slate-100
                               transition">

                        <i class="bi bi-bell"></i>

                    </button>


                    {{-- User --}}
                    <div class="flex items-center gap-2">

                        <div
                            class="w-9 h-9 rounded-full
                                   bg-indigo-100
                                   flex items-center justify-center">

                            <i class="bi bi-person-fill text-indigo-600"></i>

                        </div>

                        <div class="hidden sm:block leading-tight">

                            <p
                                class="text-sm font-semibold
                                       text-slate-700">

                                {{ auth()->user()->name }}

                            </p>

                            <p
                                class="text-[11px] text-slate-400
                                       capitalize">

                                {{ auth()->user()->role }}

                            </p>

                        </div>

                        

                    </div>

                </div>

            </header>


            {{-- =================================================
                 CONTENT
            ================================================== --}}
            <main class="flex-1 overflow-y-auto">

                <div
                    class="p-6 lg:p-8
                           max-w-[1600px] mx-auto">

                    @yield('content')

                </div>

            </main>

        </div>

    </div>


    @stack('scripts')

</body>

</html>