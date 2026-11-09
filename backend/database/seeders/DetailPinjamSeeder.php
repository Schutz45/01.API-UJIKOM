<?php

namespace Database\Seeders;

use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use App\Models\UnitAlat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPinjamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Selain membuat detail_pinjam, seeder ini juga menjalankan alokasi unit
     * (detail_pinjam_unit) untuk peminjaman berstatus dipinjam/dikembalikan,
     * menyimulasikan proses persetujuan oleh petugas/admin. Tanpa ini, unit
     * tidak akan tercatat nomor serinya dan proses pengembalian menjadi bermasalah.
     */
    public function run(): void
    {
        $details = [
            ['peminjaman_id'    =>  1,  'alat_id'  =>  1, 'jumlah'    =>  2], // Pinjam 2 Mikrotik
            ['peminjaman_id'    =>  2,  'alat_id'  =>  2, 'jumlah'    =>  1], // Pinjam 1 Kamera
            ['peminjaman_id'    =>  3,  'alat_id'  =>  3, 'jumlah'    =>  1], // Pinjam 1 Mini PC
            ['peminjaman_id'    =>  4,  'alat_id'  =>  4, 'jumlah'    =>  2], // Pinjam 2 Tang Crimping
            ['peminjaman_id'    =>  5,  'alat_id'  =>  5, 'jumlah'    =>  3], // Pinjam 3 Adapter
        ];

        foreach ($details as $detail) {
            DetailPinjam::create($detail);
        }

        // Alokasi unit untuk peminjaman yang sudah disetujui (bukan 'diajukan')
        foreach ($details as $item) {
            $peminjaman = Peminjaman::find($item['peminjaman_id']);

            if (!$peminjaman || $peminjaman->status === 'diajukan') {
                continue;
            }

            $detail = DetailPinjam::where('peminjaman_id', $item['peminjaman_id'])
                ->where('alat_id', $item['alat_id'])
                ->first();

            if (!$detail) {
                continue;
            }

            // Ambil unit tersedia milik alat tersebut (kunci untuk konsistensi)
            $units = UnitAlat::where('alat_id', $item['alat_id'])
                ->where('status', 'tersedia')
                ->orderBy('nomor_seri')
                ->take($item['jumlah'])
                ->get();

            if ($units->count() < $item['jumlah']) {
                continue; // skip kalau unit tidak cukup
            }

            foreach ($units as $unit) {
                $unit->update(['status' => 'dipinjam']);
                $detail->unitAlat()->attach($unit->id, ['kondisi_keluar' => $unit->kondisi]);

                // Untuk yang sudah dikembalikan, kembalikan unit ke tersedia & catat kondisi masuk
                if ($peminjaman->status === 'dikembalikan') {
                    $unit->update(['status' => 'tersedia', 'kondisi' => 'baik']);
                    $detail->unitAlat()->updateExistingPivot($unit->id, ['kondisi_masuk' => 'baik']);
                }
            }

            if ($peminjaman->status === 'dikembalikan') {
                $detail->update(['jumlah_rusak' => 0]);
            }
        }
    }
}
