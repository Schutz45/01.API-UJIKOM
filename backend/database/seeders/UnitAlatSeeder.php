<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnitAlatSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua alat beserta stok lama
        $alats = DB::table('alat')->get();

        foreach ($alats as $alat) {
            $stokBaik   = (int) ($alat->stok ?? 0);
            $stokRusak  = (int) ($alat->stok_rusak ?? 0);
            $total      = $stokBaik + $stokRusak;

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
