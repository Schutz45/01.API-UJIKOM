<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Observers\PeminjamanObserver;
use App\Observers\PengembalianObserver;
use App\Observers\AlatObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Peminjaman::observe(PeminjamanObserver::class);
        Pengembalian::observe(PengembalianObserver::class);
        Alat::observe(AlatObserver::class);

        /*
        |----------------------------------------------------------------------
        | View Composer: Share data badge & lonceng ke semua view secara global
        |----------------------------------------------------------------------
        | Agar komponen navbar/sidebar bisa menampilkan angka tanpa
        | harus memanggil query manual di setiap controller.
        |----------------------------------------------------------------------
        */
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!$user) {
                return;
            }

            // Lonceng: jumlah notifikasi belum dibaca
            $jumlahNotifikasi = $user->jumlahNotifikasiBelumDibaca();

            // Badge: pekerjaan yang perlu tindakan per role
            $badges = [];

            if ($user->role === 'petugas') {
                $badges['peminjaman'] = Peminjaman::where('status', 'diajukan')->count();
                $badges['pengembalian'] = Peminjaman::where('permintaan_pengembalian', true)
                    ->whereIn('status', ['dipinjam', 'telat'])
                    ->count();
            } elseif ($user->role === 'admin') {
                $badges['alat_rusak'] = Alat::where('stok_rusak', '>', 0)->count();
            }

            $view->with(compact('jumlahNotifikasi', 'badges'));
        });
    }
}
