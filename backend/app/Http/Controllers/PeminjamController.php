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
            ->where('stok', '>', 0)
            ->where('status_kondisi', 'baik')
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
                    $alat = Alat::findOrFail($alatId);

                    // Pastikan alat masih dalam kondisi baik
                    if (strtolower(trim($alat->status_kondisi)) !== 'baik') {
                        throw new \Exception("Alat {$alat->nama_alat} sedang tidak dapat dipinjam.");
                    }

                    // Pastikan stok cukup
                    if ($jumlah > $alat->stok) {
                        throw new \Exception("Jumlah {$alat->nama_alat} yang diminta melebihi stok tersedia.");
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

            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans    =   Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

            return view('peminjam.riwayat', compact('peminjamans'));
    }

    public function ajukanPengembalian($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Hanya peminjaman aktif yang bisa diajukan untuk pengembalian
            if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Peminjaman ini tidak dapat diajukan untuk pengembalian.');
            }

            // Pastikan peminjaman tersebut milik user yang sedang login
            if ($peminjaman->user_id !== auth()->id()) {
                return redirect()
                    ->back()
                    ->with('error', 'Anda tidak memiliki akses ke peminjaman ini.');
            }

            // Cegah pengajuan pengembalian berulang
            if ($peminjaman->permintaan_pengembalian) {
                return redirect()
                    ->back()
                    ->with('error', 'Pengembalian untuk peminjaman ini sudah diajukan.');
            }

            // Tandai bahwa peminjam meminta pengembalian
            $peminjaman->permintaan_pengembalian = true;
            $peminjaman->save();

            return redirect()
                ->back()
                ->with('success', 'Permintaan pengembalian berhasil dikirim kepada Petugas.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
