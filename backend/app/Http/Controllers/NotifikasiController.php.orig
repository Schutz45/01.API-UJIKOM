<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Ambil daftar notifikasi user yang sedang login.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $notifikasi = $user->notifikasi()
            ->latest()
            ->take(10)
            ->get();

        $jumlahBelumDibaca = $user->jumlahNotifikasiBelumDibaca();

        return response()->json([
            'jumlah_belum_dibaca' => $jumlahBelumDibaca,
            'data' => $notifikasi,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function tandaiDibaca(Notifikasi $notifikasi): RedirectResponse
    {
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->update([
            'dibaca' => true
        ]);

        if ($notifikasi->url_tujuan) {
            return redirect($notifikasi->url_tujuan);
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function tandaiSemuaDibaca(Request $request): JsonResponse
    {
        $request->user()
            ->notifikasi()
            ->where('dibaca', false)
            ->update([
                'dibaca' => true
            ]);

        return response()->json([
            'message' => 'Semua notifikasi ditandai telah dibaca.',
            'jumlah_belum_dibaca' => 0,
        ]);
    }

    /**
     * Badge pekerjaan yang membutuhkan tindakan.
     */
    public function badges(): JsonResponse
    {
        $user = auth()->user();

        $badges = [];

        if ($user->role === 'petugas') {

            // Peminjaman menunggu persetujuan
            $badges['peminjaman'] = Peminjaman::where(
                'status',
                'diajukan'
            )->count();

            // Pengembalian menunggu pemeriksaan
            $badges['pengembalian'] = Peminjaman::where(
                'permintaan_pengembalian',
                true
            )
                ->whereIn('status', ['dipinjam', 'telat'])
                ->count();

        } elseif ($user->role === 'admin') {

            // Jumlah jenis alat yang memiliki unit rusak
            $badges['alat_rusak'] = Alat::where(
                'stok_rusak',
                '>',
                0
            )->count();
        }

        return response()->json([
            'data' => $badges,
        ]);
    }
}