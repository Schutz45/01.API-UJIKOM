<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Http\Requests\Pengembalian\UpdatePengembalianRequest;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user           =   auth()->user();
        $query          =   Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas']);
        if ($user->role === 'peminjam') {
            $query->whereHas('peminjaman', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        $pengembalian   =   $query->latest()->get();
        return response()->json([
            'message'   =>  'Riwayat pengembalian berhasil diambil.',
            'data'      =>  $pengembalian
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengembalianRequest $request):JsonResponse
    {
        try {
            $pengembalian               =   DB::transaction(function ()   use ($request) {
                $peminjaman             =   Peminjaman::with('detailPinjam.alat')->lockForUpdate()->find($request->peminjaman_id);

                // Guard transisi eksplisit: hanya dipinjam/telat yang bisa dikembalikan
                if (!$peminjaman->canTransitionTo('dikembalikan')) {
                    throw new Exception("Data ditolak. Peminjaman ini berstatus '{$peminjaman->status}', tidak dapat dikembalikan.");
                }

                $tglKembali             =   now();
                $tglKembaliPlan         =   Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $hariIni                =   Carbon::now()->startOfDay();

                // Hitung denda keterlambatan
                $hariTerlambat          =   $hariIni->greaterThan($tglKembaliPlan) ? $tglKembaliPlan->diffInDays($hariIni) : 0;
                $dendaKeterlambatan    =   $hariTerlambat * 1000;
                $dendaKerusakan         =   $request->denda ?? 0;
                $totalDenda             =   $dendaKeterlambatan + $dendaKerusakan;

                // 1. Insert data ke tabel pengembalian
                $pengembalian           =   Pengembalian::create([
                    'peminjaman_id'         =>  $peminjaman->id,
                    'tgl_kembali'           =>  $tglKembali->toDateString(),
                    'kondisi_kembali'       =>  $request->kondisi_kembali,
                    'denda_keterlambatan'   =>  $dendaKeterlambatan,
                    'denda_kerusakan'       =>  $dendaKerusakan,
                    'denda'                 =>  $totalDenda,
                    'petugas_id'            =>  auth()->id(),
                ]);

                // 2. Kembalikan (tambah) stok alat berdasarkan detail_pinjam dan kondisi
                foreach($peminjaman->detailPinjam as $detail) {
                    // Guard Kasus B: Data detail yang corrupt/negatif tidak boleh diproses
                    if ($detail->jumlah <= 0) {
                        throw new Exception("Integritas data terganggu: jumlah pinjam untuk alat ID #{$detail->alat_id} bernilai tidak valid ({$detail->jumlah}).");
                    }

                    $alat = $detail->alat;
                    if ($alat) {
                        $jmlRusak = 0;
                        if ($request->kondisi_kembali === 'rusak') {
                            if (isset($request->jumlah_rusak[$alat->id])) {
                                $inputRusak = (int)$request->jumlah_rusak[$alat->id];
                                // Guard Kasus A: Nilai negatif dari input ditolak secara tegas
                                if ($inputRusak < 0) {
                                    throw new Exception("Jumlah unit rusak tidak boleh bernilai negatif.");
                                }
                                $jmlRusak = $inputRusak;
                            } else {
                                $jmlRusak = $detail->jumlah; // Default: Semua rusak jika kondisi global 'rusak'
                            }
                        }

                        // Stock Guard Eksplisit: Pastikan batas aman secara matematis
                        $jmlRusak = max(0, min($jmlRusak, $detail->jumlah));
                        $jmlBaik  = max(0, $detail->jumlah - $jmlRusak);

                        // Verifikasi konservasi kuantitas
                        if (($jmlBaik + $jmlRusak) !== $detail->jumlah) {
                            throw new Exception("Perhitungan stok gagal: total unit kembali tidak sesuai dengan unit pinjam.");
                        }

                        if ($jmlBaik > 0) {
                            // Kembalikan unit ke tersedia
                            $alat->unitAlat()
                                ->where('kondisi', 'baik')
                                ->orderBy('nomor_seri')
                                ->take($jmlBaik)
                                ->update(['status' => 'tersedia']);
                        }
                        if ($jmlRusak > 0) {
                            // Tandai unit rusak
                            $alat->unitAlat()
                                ->where('kondisi', 'baik')
                                ->orderBy('nomor_seri')
                                ->take($jmlRusak)
                                ->update(['status' => 'rusak', 'kondisi' => 'rusak']);
                        }

                        // Simpan info kerusakan ke detail_pinjam untuk history
                        $detail->update(['jumlah_rusak' => $jmlRusak]);

                        if ($jmlRusak > 0) {
                            \App\Services\NotifikasiService::alatRusakBaru($alat, $jmlRusak);
                        }
                    }
                }

                // 3. Ubah status di tabel peminjaman utama
                $peminjaman->update([
                    'status' => 'dikembalikan',
                    'permintaan_pengembalian' => false
                ]);

                auth()->user()->logAktivitas()->create([
                    'jenis'     =>  'pengembalian',
                    'aktivitas' =>  "Memproses pengembalian ID: #{$peminjaman->id}. Denda: Rp" . number_format($totalDenda, 0, ',', '.')
                ]);

                return $pengembalian->load(['peminjaman.user', 'petugas']);
            });

            return response()->json([
                'message'   =>  'Proses pengembalian alat berhasil diselesaikan.',
                'data'      =>  $pengembalian
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message'  =>  $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengembalian $pengembalian): JsonResponse
    {
        $user   =   auth()->user();
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas']);

        // Otorisasi privasi
        if ($user->role === 'peminjam' && $pengembalian->peminjaman->user_id !== $user->id) {
            return response()->json(['message'  =>  'Akses ditolak.'], 403);
        }

        return response()->json([
            'message'   =>  'Detail pengembalian berhasil diambil.',
            'data'      =>  $pengembalian
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengembalianRequest $request, Pengembalian $pengembalian): JsonResponse
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'petugas'])) {
            return response()->json(['message' => 'Hanya Admin/Petugas yang dapat memperbarui data pengembalian.'], 403);
        }

        $pengembalian->update([
            'kondisi_kembali'   =>  $request->kondisi_kembali,
            'denda_kerusakan'   =>  $request->denda ?? $pengembalian->denda_kerusakan,
            // Re-calculate total denda if needed
            'denda'             =>  $pengembalian->denda_keterlambatan + ($request->denda ?? $pengembalian->denda_kerusakan)
        ]);

        return response()->json([
            'message'   =>  'Data pengembalian berhasil diperbarui.',
            'data'      =>  $pengembalian->load(['peminjaman.user', 'petugas'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengembalian $pengembalian): JsonResponse
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Hanya Admin yang dapat menghapus riwayat pengembalian.'], 403);
        }

        try {
            DB::transaction(function () use ($pengembalian) {
                $peminjaman = Peminjaman::with('detailPinjam')->lockForUpdate()->findOrFail($pengembalian->peminjaman_id);

                // Tarik kembali stok ke gudang berdasarkan kondisi sebelumnya
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);

                    $jmlRusak = $detail->jumlah_rusak;
                    $jmlBaik  = $detail->jumlah - $jmlRusak;

                    // Kembalikan unit baik menjadi dipinjam (ditarik dari stok)
                    if ($jmlBaik > 0) {
                        $alat->unitAlat()
                            ->where('kondisi', 'baik')
                            ->where('status', 'tersedia')
                            ->orderBy('nomor_seri')
                            ->take($jmlBaik)
                            ->update(['status' => 'dipinjam']);
                    }
                    // Kembalikan unit rusak menjadi dipinjam lagi
                    if ($jmlRusak > 0) {
                        $alat->unitAlat()
                            ->where('kondisi', 'rusak')
                            ->orderBy('nomor_seri')
                            ->take($jmlRusak)
                            ->update(['status' => 'dipinjam', 'kondisi' => 'baik']);
                    }
                    $detail->update(['jumlah_rusak' => 0]);
                }

                $peminjaman->update(['status' => 'dipinjam']);
                auth()->user()->logAktivitas()?->create(['jenis' => 'pengembalian', 'aktivitas' => "Membatalkan pengembalian ID: #{$pengembalian->id}"]);
                $pengembalian->delete();
            });

            return response()->json([
                'message'   =>  'Data pengembalian berhasil dihapus. Stok dan status peminjaman telah dikembalikan ke kondisi semula.'
            ]);
        } catch (Exception $e) {
            return response()->json(['message'  =>  $e->getMessage()], 422);
        }
    }
}
