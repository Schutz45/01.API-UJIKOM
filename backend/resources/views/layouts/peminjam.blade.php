<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SIPPSD - Sistem Informasi Peminjaman Peralatan Sekolah Digital')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

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
                        class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-tools text-xl"></i>
                    </div>

                    <div class="min-w-0">

                        <h1 class="text-lg font-bold tracking-wide">
                            SIPPSD
                        </h1>

                        <p class="text-[9px] leading-tight text-slate-400">
                            Sistem Peminjaman Alat
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
                    src="{{ asset('storage/images/Megumin-SidebarV2.png') }}"
                    alt=""
                    class="absolute inset-0
                        w-full h-full
                        object-cover 
                        object-[10%_center]
                        opacity-45">
                
                {{-- Lapisan gelap agar menu tetap terbaca --}}
                <div class="absolute inset-0 bg-slate-950/25"></div>

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
                <button
                    type="button"
                    onclick="openLogoutModal()"
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
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
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
                    <div class="relative" id="notificationWrapper">
                        <button
                            type="button"
                            id="notificationButton"
                            class="relative w-9 h-9 rounded-full
                                   flex items-center justify-center
                                   text-slate-500
                                   hover:bg-slate-100
                                   transition">

                            <i class="bi bi-bell"></i>

                            @if(($jumlahNotifikasi ?? 0) > 0)
                                <span
                                    id="notificationBadge"
                                    class="absolute top-1 right-1
                                           flex h-4 w-4 items-center justify-center
                                           rounded-full bg-red-500
                                           text-[10px] font-bold text-white shadow-sm border border-white">
                                    {{ $jumlahNotifikasi }}
                                </span>
                            @endif
                        </button>

                        {{-- DROPDOWN NOTIFIKASI --}}
                        <div
                            id="notificationDropdown"
                            class="hidden absolute right-0 top-12
                                   w-80 bg-white
                                   border border-slate-200
                                   rounded-xl shadow-xl
                                   z-50 overflow-hidden">

                            {{-- Header --}}
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <h3 class="text-sm font-semibold text-slate-800">Notifikasi</h3>
                                <button
                                    type="button"
                                    id="markAllReadButton"
                                    class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                    Tandai semua
                                </button>
                            </div>

                            {{-- Isi notifikasi --}}
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <div class="px-4 py-8 text-center text-sm text-slate-400">Memuat notifikasi...</div>
                            </div>
                        </div>
                    </div>


                    {{-- User --}}
                    <div class="flex items-center gap-2">

                        <div
                            class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
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


        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationButton = document.getElementById('notificationButton');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const markAllReadButton = document.getElementById('markAllReadButton');

            if (!notificationButton || !notificationDropdown) return;

            // Buka/Tutup Dropdown
            notificationButton.addEventListener('click', function (event) {
                event.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            });

            // Klik di luar dropdown
            document.addEventListener('click', function (event) {
                const wrapper = document.getElementById('notificationWrapper');
                if (wrapper && !wrapper.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });

            // Tandai Semua Dibaca
            if (markAllReadButton) {
                markAllReadButton.addEventListener('click', async function () {
                    try {
                        const response = await fetch("{{ route('notifikasi.bacaSemua') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (!response.ok) throw new Error('Gagal menandai semua dibaca');
                        loadNotifications();
                        const badge = document.getElementById('notificationBadge');
                        if (badge) badge.remove();
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            // Load Notifikasi
            async function loadNotifications() {
                notificationList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-400">Memuat...</div>';
                try {
                    const response = await fetch("{{ route('notifikasi.index') }}", {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok) throw new Error('Gagal mengambil data');
                    const result = await response.json();
                    renderNotifications(result.data || []);
                    updateBadge(result.jumlah_belum_dibaca);
                } catch (error) {
                    notificationList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-red-500">Gagal memuat.</div>';
                }
            }

            function updateBadge(count) {
                let badge = document.getElementById('notificationBadge');
                if (count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.id = 'notificationBadge';
                        badge.className = 'absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm border border-white';
                        notificationButton.appendChild(badge);
                    }
                    badge.textContent = count;
                } else if (badge) {
                    badge.remove();
                }
            }

            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-400"><i class="bi bi-bell-slash text-xl block mb-2"></i>Tidak ada notifikasi.</div>';
                    return;
                }

                notificationList.innerHTML = notifications.map(n => {
                    const dibacaClass = n.dibaca ? '' : 'bg-indigo-50/50';
                    const icon = n.jenis === 'peminjaman' ? 'bi-box-seam' : (n.jenis === 'pengembalian' ? 'bi-arrow-left-right' : 'bi-bell');
                    
                    return `
                        <div class="relative border-b border-slate-50 last:border-0">
                            <form id="form-notif-${n.id}" method="POST" action="{{ url('/notifikasi') }}/${n.id}/dibaca" class="hidden">
                                @csrf
                            </form>
                            <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('form-notif-${n.id}').submit();"
                               class="block px-4 py-3 hover:bg-slate-50 transition ${dibacaClass}">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <i class="bi ${icon}"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800">${escapeHtml(n.judul)}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">${escapeHtml(n.pesan)}</p>
                                        <p class="text-[10px] text-slate-400 mt-1">${formatTime(n.created_at)}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                }).join('');
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }

            function formatTime(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                const now = new Date();
                const diff = (now - date) / 1000;
                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
            }
        });
    </script>

    @stack('scripts')

    {{-- Modal Konfirmasi Logout --}}
    <div id="logoutModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="closeLogoutModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-500 shrink-0">
                            <i class="bi bi-box-arrow-right text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-800" id="modal-title">
                                Keluar dari Akun?
                            </h3>
                            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                                Sesi peminjamanmu akan berakhir. Kamu harus masuk kembali untuk meminjam atau memantau riwayat alat.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="closeLogoutModal()"
                            class="flex-1 px-4 py-3 text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="button" onclick="document.getElementById('logout-form').submit()"
                            class="flex-1 px-4 py-3 text-sm font-bold text-white bg-red-500 rounded-2xl hover:bg-red-600 transition shadow-sm shadow-red-500/20">
                            Ya, Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLogoutModal();
        });
    </script>
</body>

</html>