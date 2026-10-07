<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitAlatSeeder extends Seeder
{
    /**
     * Konfigurasi jumlah unit per alat.
     * Kunci array menunjukkan ID alat.
     */
    private array $konfigurasiUnit = [
        1 => ['baik' => 15, 'rusak' => 0],  // Router Mikrotik RB941-2nD
        2 => ['baik' => 5,  'rusak' => 0],  // Kamera DSLR Canon EOS 3000D
        3 => ['baik' => 8,  'rusak' => 0],  // Mini PC Intel NUC 11
        4 => ['baik' => 20, 'rusak' => 0],  // Tang Crimping RJ45/RJ11 Proskit
        5 => ['baik' => 25, 'rusak' => 0],  // Adapter HDMI to VGA dengan Audio
    ];

    public function run(): void
    {
        // Ambil semua alat
        $alats = DB::table('alat')->get();

        foreach ($alats as $alat) {
            // Ambil konfigurasi unit berdasarkan ID alat
            $konfigurasi = $this->konfigurasiUnit[$alat->id] ?? ['baik' => 0, 'rusak' => 0];

            $stokBaik   = (int) ($konfigurasi['baik'] ?? 0);
            $stokRusak  = (int) ($konfigurasi['rusak'] ?? 0);

            // Ambil huruf depan nama alat sebagai prefix nomor seri
            $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $alat->nama_alat));
            $prefix = substr($prefix, 0, 4);

            $now = now();

            // Buat unit untuk stok baik
            for ($i = 1; $i <= $stokBaik; $i++) {
                DB::table('unit_alat')->insert([
                    'alat_id'    => $alat->id,
                    'nomor_seri' => $prefix . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'status'     => 'tersedia',
                    'kondisi'    => 'baik',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Buat unit untuk stok rusak
            for ($i = 1; $i <= $stokRusak; $i++) {
                DB::table('unit_alat')->insert([
                    'alat_id'    => $alat->id,
                    'nomor_seri' => $prefix . '-' . str_pad($stokBaik + $i, 3, '0', STR_PAD_LEFT),
                    'status'     => 'rusak',
                    'kondisi'    => 'rusak',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        echo "Unit alat berhasil dibuat." . PHP_EOL;
    }
}
