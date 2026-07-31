<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanObserver
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
     * Handle the Peminjaman "created" event.
     */
    public function created(Peminjaman $peminjaman): void
    {
        $namaPeminjam   =   $peminjaman->user?->name ?? 'User';
        $this->catalog("Peminjam ({$namaPeminjam}) membuat permohonan peminjaman baru (ID: #{$peminjaman->id})");
    }

    /**
     * Handle the Peminjaman "updated" event.
     */
    public function updated(Peminjaman $peminjaman): void
    {
        if ($peminjaman->wasChanged("status")) {
            $this->catalog("Status peminjaman (ID: #{$peminjaman->id}) berubah menjadi: '{$peminjaman->status}'");
        } else {
            if (!empty($peminjaman->getChanges())) {
                $this->catalog("Meperbarui detail data peminjaman (ID: #{$peminjaman->id})");
            }
        }
    }

    /**
     * Handle the Peminjaman "deleted" event.
     */
    public function deleted(Peminjaman $peminjaman): void
    {
        $this->catalog("Mebatalkan/menghapus permohonan peminjaman (ID: #{$peminjaman->id})");
    }
}
