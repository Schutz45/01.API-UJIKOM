<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Catatan: status "telat" tidak perlu disimpan ke database.
     * Ia dihitung otomatis oleh accessor Peminjaman::getStatusAttribute()
     * saat status="dipinjam" dan tgl_kembali_plan sudah lewat.
     */
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id'           =>  3,  // Rian (Peminjam)
                'tgl_pinjam'        =>  '2026-06-01',
                'tgl_kembali_plan'  =>  '2026-06-04',
                'status'            =>  'dikembalikan',
            ],
            [
                'user_id'           =>  4,  // Siti (Peminjam)
                'tgl_pinjam'        =>  '2026-06-02',
                'tgl_kembali_plan'  =>  '2026-06-05',
                'status'            =>  'dikembalikan',
            ],
            [
                // Status tetap "dipinjam" di DB; accessor akan menampilkan "telat"
                // karena tgl_kembali_plan (2026-06-06) sudah lewat dari tanggal sekarang.
                'user_id'           =>  5,  // Eka (Peminjam)
                'tgl_pinjam'        =>  '2026-06-03',
                'tgl_kembali_plan'  =>  '2026-06-06',
                'status'            =>  'dipinjam',
            ],
            [
                'user_id'           =>  3,
                'tgl_pinjam'        =>  '2026-06-08',
                'tgl_kembali_plan'  =>  '2026-06-11',
                'status'            =>  'dipinjam',
            ],
            [
                'user_id'           =>  4,
                'tgl_pinjam'        =>  '2026-06-09',
                'tgl_kembali_plan'  =>  '2026-06-12',
                'status'            =>  'diajukan',
            ],
        ];

        foreach ($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}
