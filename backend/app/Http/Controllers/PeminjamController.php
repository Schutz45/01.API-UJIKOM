<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats  =   Alat::with('kategori')
            ->tersedia()
            ->get();

        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan'  =>  ['required', 'date', 'after:today'],

            'alat_id'           =>  ['required', 'array', 'min:1'],
            'alat_id.*'         =>  ['required', 'integer', 'exists:alat,id', 'distinct'],

            'jumlah'            =>  ['required', 'array'],
            'jumlah.*'          =>  ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            // Cek setiap alat yang dipilih
            foreach ($request->alat_id as $alatId) {

                    // Ambil jumlah berdasarkan ID alat
                    $jumlah = (int) ($request->jumlah[$alatId] ?? 0);

                    if ($jumlah < 1) {
                        throw new \Exception('Jumlah alat tidak valid.');
                        }

                    // Cari alat
                    $alat = Alat::lockForUpdate()->findOrFail($alatId);

                    // Pastikan jumlah unit tersedia cukup (sistem UnitAlat)
                    $unitTersedia = $alat->unitAlat()
                        ->where('status', 'tersedia')
                        ->lockForUpdate()
                        ->count();

                    if ($jumlah > $unitTersedia) {
                        throw new \Exception("Jumlah {$alat->nama_alat} yang diminta melebihi stok tersedia ({$unitTersedia} unit).");
                    }
            }

            // Buat data peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'           =>  auth()->id(),
                'tgl_pinjam'        =>  now(),
                'tgl_kembali_plan'  =>  $request->tgl_kembali_plan,
                'status'            =>  'diajukan',
            ]);

            // Simpan detail alat yang dipilih
            foreach ($request->alat_id as $alatId) {
                $jumlah = (int) $request->jumlah[$alatId];

                DetailPinjam::create([
                    'peminjaman_id' =>  $peminjaman->id,
                    'alat_id'       =>  $alatId,
                    'jumlah'        =>  $jumlah,
                ]);
            }

            DB::commit();

            // Kirim notifikasi secara terpisah.
            // Kegagalan notifikasi tidak membatalkan pengajuan yang sudah tersimpan.
            try {
                \App\Services\NotifikasiService::pengajuanDiajukan($peminjaman);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Gagal mengirim notifikasi pengajuan peminjaman.',
                    [
                        'peminjaman_id' => $peminjaman->id,
                        'error' => $e->getMessage(),
                    ]
                );
            }

            return redirect()
                ->route('peminjam.riwayat')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
            } catch (\Throwable $e) {
                DB::rollback();

                return redirect()->back()
                    ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
            }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans    =   Peminjaman::with(['detailPinjam.alat', 'detailPinjam.unitAlat'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

            return view('peminjam.riwayat', compact('peminjamans'));
    }

    public function ajukanPengembalian($id)
    {
        try {
            // Proses perubahan status menggunakan transaksi database.
            $peminjaman = DB::transaction(function () use ($id) {

                // Cari peminjaman yang benar-benar milik pengguna login.
                // lockForUpdate mencegah proses bersamaan mengubah data yang sama.
                $peminjaman = Peminjaman::where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->findOrFail($id);

                // Hanya peminjaman aktif yang dapat diajukan untuk pengembalian.
                if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
                    throw new \DomainException(
                        'Peminjaman ini tidak dapat diajukan untuk pengembalian.'
                    );
                }

                // Cegah pengajuan pengembalian berulang.
                if ($peminjaman->permintaan_pengembalian) {
                    throw new \DomainException(
                        'Pengembalian untuk peminjaman ini sudah diajukan.'
                    );
                }

                // Simpan permintaan pengembalian.
                $peminjaman->update([
                    'permintaan_pengembalian' => true,
                ]);

                return $peminjaman;
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return redirect()->back()
                ->with('error', 'Peminjaman tidak ditemukan atau Anda tidak memiliki akses.');

        } catch (\DomainException $e) {

            return redirect()->back()
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {

            \Illuminate\Support\Facades\Log::error(
                'Gagal mengajukan pengembalian.',
                [
                    'peminjaman_id' => $id,
                    'user_id' => auth()->id(),
                    'error' => $e->getMessage(),
                ]
            );

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengembalian.');
        }

        // Kirim notifikasi setelah transaksi berhasil.
        // Kegagalan notifikasi tidak membatalkan permintaan pengembalian.
        try {
            \App\Services\NotifikasiService::permintaanPengembalian($peminjaman);

        } catch (\Throwable $e) {

            \Illuminate\Support\Facades\Log::error(
                'Gagal mengirim notifikasi permintaan pengembalian.',
                [
                    'peminjaman_id' => $peminjaman->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        return redirect()->back()
            ->with('success', 'Permintaan pengembalian berhasil dikirim kepada Petugas.');
    }
}
