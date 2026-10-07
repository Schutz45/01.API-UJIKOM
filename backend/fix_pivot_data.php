<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Untuk setiap detail_pinjam yang punya jumlah_rusak > 0 tapi pivot-nya kosong,
//    ambil unit terkecil (nomor_seri) alat tersebut, tandai rusak, lalu buat pivot.
$details = DB::table('detail_pinjam')
    ->where('jumlah_rusak', '>', 0)
    ->orderBy('id')
    ->get();

$created = 0;

foreach ($details as $detail) {
    $hasPivot = DB::table('detail_pinjam_unit')
        ->where('detail_pinjam_id', $detail->id)
        ->exists();

    if ($hasPivot) {
        continue;
    }

    // Ambil unit milik alat ini dengan id terkecil (nomor seri paling awal)
    $units = DB::table('unit_alat')
        ->where('alat_id', $detail->alat_id)
        ->orderBy('id')
        ->limit($detail->jumlah_rusak)
        ->get();

    foreach ($units as $unit) {
        DB::table('unit_alat')
            ->where('id', $unit->id)
            ->update([
                'status'  => 'rusak',
                'kondisi' => 'rusak',
            ]);

        DB::table('detail_pinjam_unit')->insert([
            'detail_pinjam_id' => $detail->id,
            'unit_alat_id'     => $unit->id,
            'kondisi_keluar'   => 'baik',
            'kondisi_masuk'    => 'rusak',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $created++;
    }

    echo "Detail #{$detail->id} (alat {$detail->alat_id}): dibuat {$units->count()} pivot\n";
}

// 2. Sinkronisasi kondisi_masuk untuk seluruh pivot: anggap kondisi_masuk rusak
//    hanya jika unit saat ini rusak, agar konsisten dengan data unit_alat.
$sinkron = 0;
$pivots = DB::table('detail_pinjam_unit')->get();

foreach ($pivots as $pivot) {
    $unit = DB::table('unit_alat')->where('id', $pivot->unit_alat_id)->first();

    if (!$unit) {
        continue;
    }

    $kondisiMasuk = ($unit->kondisi === 'rusak') ? 'rusak' : 'baik';

    if ($pivot->kondisi_masuk !== $kondisiMasuk) {
        DB::table('detail_pinjam_unit')
            ->where('detail_pinjam_id', $pivot->detail_pinjam_id)
            ->where('unit_alat_id', $pivot->unit_alat_id)
            ->update(['kondisi_masuk' => $kondisiMasuk]);
        $sinkron++;
    }
}

echo "Total pivot dibuat: {$created}\n";
echo "Pivot disinkronkan: {$sinkron}\n";
echo "Selesai.\n";
