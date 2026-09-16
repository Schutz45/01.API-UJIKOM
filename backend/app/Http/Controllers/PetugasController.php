<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans =   Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman =   Peminjaman::with('detailPinjam.alat')->findOrFail($id);

            // Pastikan hanya pengajuan yang bisa disetujui
            if ($peminjaman->status !== 'diajukan') {
                return redirect()->back()->with('error', 'Peminjaman sudah diproses.');
            }

            // Cek stok semua alat terlebih dahulu
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                if ($alat->stok < $detail->jumlah) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }
            }

            // Kurangi stok alat setelah semua stok dinyatakan cukup
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->decrement('stok', $detail->jumlah);
            }

            // Ubah status menjadi dipinjam
            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali'   =>  'required|in:baik,rusak',
            'denda'             =>  'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($peminjamanId);

            // Pastikan peminjaman masih aktif
            if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                throw new \Exception('Peminjaman ini sudah tidak dapat diproses untuk pengembalian.');
            }

            // Tanggal Pengembalian menggunakan tanggal server
            $tglKembali = now();

            // Hitung keterlambatan
            $tanggalRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);

            $hariTerlambat  = 0;

            if ($tglKembali->startOfDay()->gt($tanggalRencana->startOfDay())) {
                $hariTerlambat = $tanggalRencana->diffInDays($tglKembali->startOfDay());
            }

            // Denda keterlambatan Rp 1.000 per hari
            $dendaKeterlambatan = $hariTerlambat * 1000;

            // Denda kerusakan dimasukkan Petugas secara manual
            $dendaKerusakan = $request->denda ?? 0;

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id'         =>  $peminjaman->id,
                'tgl_kembali'           =>  $tglKembali,
                'kondisi_kembali'       =>  $request->kondisi_kembali,
                'denda_keterlambatan'   =>  $dendaKeterlambatan,
                'denda_kerusakan'       =>  $dendaKerusakan,
                'denda'                 =>  $totalDenda,
                'petugas_id'            =>  auth()->id(),
            ]);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                if ($alat) {
                    $alat->increment('stok', $detail->jumlah);
                }
            }

            // Ubah status peminjaman
            $peminjaman->update([
                'status' => 'dikembalikan',
                'permintaan_pengembalian' => false,
            ]);

            DB::commit();

            return redirect()
                ->route('petugas.pengembalian.index')
                ->with(
                    'success', 
                    'Pengembalian berhasil diproses. Total denda: Rp ' . number_format($totalDenda, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        try{
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexPengembalian()
    {
        Peminjaman::where('status', 'dipinjam')
            ->whereDate('tgl_kembali_plan', '<', now()->toDateString())
            ->update(['status' => 'telat']);

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get();

            return view('petugas.pengembalian.index', compact('peminjamans'));
    }

    public function createPengembalian($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjam.alat'
        ])->findOrFail($id);

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()
                ->route('petugas.pengembalian.index')
                ->with('error', 'Peminjaman ini tidak dapat diproses untuk pengembalian.');
        }

        return view('petugas.pengembalian.create', compact('peminjaman'));
    }

    public function indexLaporan()
    {
        $dari   = request()->input('dari');
        $sampai = request()->input('sampai');

        // data laporan peminjaman
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($dari, function ($query, $dari) {
                return $query->whereDate('tgl_pinjam', '>=', $dari);
            })
            ->when($sampai, function ($query, $sampai) {
                return $query->whereDate('tgl_pinjam', '<=', $sampai);
            })
            ->latest()
            ->get();

        // data laporan pengembalian
        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas'])
            ->when($dari, function ($query, $dari) {
                return $query->whereDate('tgl_kembali', '>=', $dari);
            })
            ->when($sampai, function ($query, $sampai) {
                return $query->whereDate('tgl_kembali', '<=', $sampai);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact(
            'peminjamans',
            'pengembalians',
            'dari',
            'sampai'
        ));
    }
    
    public function cetakLaporan(Request $request)
    {
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjam.alat'
        ])
        ->when($dari, function ($query, $dari) {
            return $query->whereDate('tgl_pinjam', '>=', $dari);
        })
        ->when($sampai, function ($query, $sampai) {
            return $query->whereDate('tgl_pinjam', '<=', $sampai);
        })
        ->latest()
        ->get();

        $pengembalians = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ])
        ->when($dari, function ($query, $dari) {
            return $query->whereDate('tgl_kembali', '>=', $dari);
        })
        ->when($sampai, function ($query, $sampai) {
            return $query->whereDate('tgl_kembali', '<=', $sampai);
        })
        ->latest()
        ->get();

        return view('petugas.laporan.cetak', compact(
            'peminjamans',
            'pengembalians',
            'dari',
            'sampai'
        ));
    }
}
