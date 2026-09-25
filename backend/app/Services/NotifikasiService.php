<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\User;

class NotifikasiService
{
    /*
    |----------------------------------------------------------------------
    | PETUGAS (notifikasi tindakan / pekerjaan)
    |----------------------------------------------------------------------
    */

    /**
     * Pengajuan peminjaman baru oleh peminjam.
     * → petugas harus memproses persetujuan.
     */
    public static function pengajuanBaru(Peminjaman $peminjaman): void
    {
        $namaPeminjam = $peminjaman->user?->name ?? 'Peminjam';

        foreach (self::petugas() as $petugas) {
            Notifikasi::kirim(
                userId: $petugas->id,
                jenis: 'tindakan',
                judul: 'Pengajuan Peminjaman Baru',
                pesan: "{$namaPeminjam} mengajukan peminjaman baru (#{$peminjaman->id}). Menunggu persetujuan Anda.",
                referensiTipe: 'peminjaman',
                referensiId: $peminjaman->id,
                urlTujuan: route('petugas.peminjaman.index')
            );
        }
    }

    /**
     * Peminjam mengajukan permintaan pengembalian.
     * → petugas harus memeriksa kondisi alat.
     */
    public static function permintaanPengembalian(Peminjaman $peminjaman): void
    {
        $namaPeminjam = $peminjaman->user?->name ?? 'Peminjam';

        foreach (self::petugas() as $petugas) {
            Notifikasi::kirim(
                userId: $petugas->id,
                jenis: 'tindakan',
                judul: 'Permintaan Pengembalian',
                pesan: "{$namaPeminjam} meminta pengembalian untuk peminjaman #{$peminjaman->id}. Menunggu pemeriksaan.",
                referensiTipe: 'peminjaman',
                referensiId: $peminjaman->id,
                urlTujuan: route('petugas.pengembalian.index')
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | PEMINJAM (notifikasi informasi)
    |----------------------------------------------------------------------
    */

    /**
     * Peminjam mengajukan peminjaman baru.
     */
    public static function pengajuanDiajukan(Peminjaman $peminjaman): void
    {
        Notifikasi::kirim(
            userId: $peminjaman->user_id,
            jenis: 'informasi',
            judul: 'Pengajuan Diterima',
            pesan: "Pengajuan peminjaman #{$peminjaman->id} telah diterima dan sedang diperiksa.",
            referensiTipe: 'peminjaman',
            referensiId: $peminjaman->id,
            urlTujuan: route('peminjam.riwayat')
        );
    }

    /**
     * Peminjaman disetujui petugas.
     */
    public static function peminjamanDisetujui(Peminjaman $peminjaman): void
    {
        Notifikasi::kirim(
            userId: $peminjaman->user_id,
            jenis: 'informasi',
            judul: 'Peminjaman Disetujui',
            pesan: "Peminjaman Anda (#{$peminjaman->id}) telah disetujui. Alat dapat diambil.",
            referensiTipe: 'peminjaman',
            referensiId: $peminjaman->id,
            urlTujuan: route('peminjam.riwayat')
        );
    }

    /**
     * Peminjaman ditolak petugas.
     */
    public static function peminjamanDitolak(Peminjaman $peminjaman): void
    {
        Notifikasi::kirim(
            userId: $peminjaman->user_id,
            jenis: 'informasi',
            judul: 'Peminjaman Ditolak',
            pesan: "Maaf, pengajuan peminjaman Anda (#{$peminjaman->id}) ditolak.",
            referensiTipe: 'peminjaman',
            referensiId: $peminjaman->id,
            urlTujuan: route('peminjam.riwayat')
        );
    }

    /**
     * Pengembalian selesai diproses petugas.
     */
    public static function pengembalianSelesai(Pengembalian $pengembalian): void
    {
        $peminjaman = $pengembalian->peminjaman;
        $denda = $pengembalian->denda ?? 0;

        $pesan = "Pengembalian Anda (#{$pengembalian->peminjaman_id}) telah diproses.";

        if ($denda > 0) {
            $pesan .= " Denda tercatat: Rp" . number_format($denda, 0, ',', '.');
        }

        Notifikasi::kirim(
            userId: $peminjaman->user_id,
            jenis: 'informasi',
            judul: 'Pengembalian Selesai',
            pesan: $pesan,
            referensiTipe: 'pengembalian',
            referensiId: $pengembalian->id,
            urlTujuan: route('peminjam.riwayat')
        );
    }

    /*
    |----------------------------------------------------------------------
    | ADMIN (notifikasi tindakan: alat rusak)
    |----------------------------------------------------------------------
    */

    /**
     * Alat masuk ke stok_rusak (dari pengembalian / pengecekan).
     * Dikirim sekali per jenis alat, badge = jumlah jenis alat rusak.
     */
    public static function alatRusakBaru(Alat $alat, int $jumlahRusak): void
    {
        foreach (self::admins() as $admin) {
            Notifikasi::kirim(
                userId: $admin->id,
                jenis: 'tindakan',
                judul: 'Alat Rusak Baru',
                pesan: "'{$alat->nama_alat}' tercatat rusak sebanyak {$jumlahRusak} unit dan perlu ditangani.",
                referensiTipe: 'alat',
                referensiId: $alat->id,
                urlTujuan: route('admin.alat.edit', $alat->id)
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | Helper: penerima notifikasi
    |----------------------------------------------------------------------
    */
    private static function petugas()
    {
        return User::where('role', 'petugas')->get();
    }

    private static function admins()
    {
        return User::where('role', 'admin')->get();
    }
}
