<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alat = [
            [
                'id'                => 1,
                'kategori_id'       => 1,
                'nama_alat'         => 'Router Mikrotik RB941-2nD',
                'deskripsi'         => 'Router nirkabel rumahan yang cocok untuk praktik jaringan dasar.',
                'gambar'            => 'mikrotik_rb941.jpg'
            ],
            [
                'id'                => 2,
                'kategori_id'       => 2,
                'nama_alat'         => 'Kamera DSLR Canon EOS 3000D',
                'deskripsi'         => 'Kamera pemula untuk kebutuhan dokumentasi dan pembuatan aset media.',
                'gambar'            => 'canon_3000d.jpg'
            ],
            [
                'id'                => 3,
                'kategori_id'       => 3,
                'nama_alat'         => 'Mini PC Intel NUC 11',
                'deskripsi'         => 'Perangkat komputasi ringkas untuk server lokal skala kecil.',
                'gambar'            => 'intel_nuc.jpg'
            ],
            [
                'id'                => 4,
                'kategori_id'       => 4,
                'nama_alat'         => 'Tang Crimping RJ45/RJ11 Proskit',
                'deskripsi'         => 'Alat potong dan pasang konektor kabel UTP.',
                'gambar'            => 'crimping_proskit.jpg'
            ],
            [
                'id'                => 5,
                'kategori_id'       => 5,
                'nama_alat'         => 'Adapter HDMI to VGA dengan Audio',
                'deskripsi'         => 'Konverter display untuk menyambungkan perangkat modern ke proyektor lama.',
                'gambar'            => 'hdmi_vga.jpg'
            ],
        ];

        foreach ($alat as $item) {
            Alat::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
