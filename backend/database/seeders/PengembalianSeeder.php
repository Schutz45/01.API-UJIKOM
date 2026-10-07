<?php

namespace Database\Seeders;

use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Catatan: kolom kondisi_kembali hanya boleh berisi "baik" atau "rusak"
     * (sesuai validasi di controller). Denda dipecah menjadi
     * denda_keterlambatan dan denda_kerusakan.
     */
    public function run(): void
    {
        $pengembalian = [
            [
                'peminjaman_id'         =>  1,
                'tgl_kembali'           =>  '2026-06-04', // tepat waktu
                'kondisi_kembali'       =>  'baik',
                'denda_keterlambatan'   =>  0,
                'denda_kerusakan'       =>  0,
                'denda'                 =>  0,
                'petugas_id'            =>  2, // Arif (Petugas)
            ],
            [
                'peminjaman_id'         =>  2,
                'tgl_kembali'           =>  '2026-06-05', // tepat waktu
                'kondisi_kembali'       =>  'baik',
                'denda_keterlambatan'   =>  0,
                'denda_kerusakan'       =>  0,
                'denda'                 =>  0,
                'petugas_id'            =>  2,
            ],
            [
                // Telat 3 hari dari rencana 2026-06-06 -> 3 x Rp1.000 = Rp3.000
                'peminjaman_id'         =>  3,
                'tgl_kembali'           =>  '2026-06-09',
                'kondisi_kembali'       =>  'rusak',
                'denda_keterlambatan'   =>  3000,
                'denda_kerusakan'       =>  27000,
                'denda'                 =>  30000,
                'petugas_id'            =>  2,
            ],
        ];

        foreach ($pengembalian as $kembali) {
            Pengembalian::create($kembali);
        }
    }
}
