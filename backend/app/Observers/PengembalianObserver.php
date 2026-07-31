<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PengembalianObserver
{
    private function catalog(string $pesan): void
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id'       =>  Auth::id(),
                'aktivitas'     =>  $pesan,
            ]);
        }
    }

    /**
     * Handle the Pengembalian "created" event.
     */
    public function created(Pengembalian $pengembalian): void
    {
        $this->catalog("Memproses pengembalian alat untuk Peminjaman ID: #{$pengembalian->peminjaman_id}");
    }

    /**
     * Handle the Pengembalian "updated" event.
     */
    public function updated(Pengembalian $pengembalian): void
    {
        $perubahan = array_diff(array_keys($pengembalian->getChanges()), ['updated_at']);
        if (!empty{$perubahan}) {
            $kolom  =   implode(', ', $perubahan);
            $this->catalog("Merevisi data pengembalian (ID Kembali: #{$pengembalian->id}, Peminjaman ID: #{$pengembalian->peminjaman_id}, Kolom diubah: {$kolom})");
        }
    }

    /**
     * Handle the Pengembalian "deleted" event.
     */
    public function deleted(Pengembalian $pengembalian): void
    {
        $this->catalog("Membatalkan/menghapus riwayat pengembalian (Peminjaman ID: #{$pengembalian->peminjaman_id})");
    }
}
