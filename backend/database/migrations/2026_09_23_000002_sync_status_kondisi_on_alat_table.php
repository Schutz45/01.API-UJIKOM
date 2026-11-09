<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronisasi data lama pada kolom status_kondisi agar konsisten
 * dengan aturan computed (stok & stok_rusak) di Model Alat.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('alat')->where('stok', '>', 0)->where('stok_rusak', 0)->update(['status_kondisi' => 'Baik']);
        DB::table('alat')->where('stok', '>', 0)->where('stok_rusak', '>', 0)->update(['status_kondisi' => 'Sebagian Rusak']);
        DB::table('alat')->where('stok', 0)->where('stok_rusak', '>', 0)->update(['status_kondisi' => 'Rusak']);
        DB::table('alat')->where('stok', 0)->where('stok_rusak', 0)->update(['status_kondisi' => 'Habis']);
    }

    public function down(): void
    {
        // Tidak dapat di-rollback: perubahan data historis.
    }
};
