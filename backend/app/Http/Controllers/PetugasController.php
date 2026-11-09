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

    // Menampilkan form alokasi unit (pemilihan nomor seri manual oleh Petugas)
    public function alokasiPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat.unitAlat'])
            ->where('status', 'diajukan')
            ->findOrFail($id);

        return view('petugas.peminjaman.alokasi', compact('peminjaman'));
    }

    public function setujuiPeminjaman(Request $request, $id)
    {
        $request->validate([
            'unit'              =>  ['required', 'array', 'min:1'],
            'unit.*'            =>  ['required', 'array', 'min:1'],
            'unit.*.*'          =>  ['required', 'integer', 'exists:unit_alat,id'],
        ], [
            'unit.required'     =>  'Anda harus memilih minimal satu unit alat.',
            'unit.*.min'        =>  'Jumlah unit yang dipilih harus sesuai dengan jumlah yang dipinjam.',
            'unit.*.*.exists'   =>  'Unit alat yang dipilih tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman =   Peminjaman::with('detailPinjam.alat')->lockForUpdate()->findOrFail($id);

            // Pastikan hanya pengajuan yang bisa disetujui
            if (!$peminjaman->canTransitionTo('dipinjam')) {
                return redirect()->back()->with('error', "Peminjaman ini berstatus '{$peminjaman->status}' dan tidak dapat disetujui.");
            }

            // Validasi & alokasikan unit pilihan Petugas
            foreach ($peminjaman->detailPinjam as $detail) {
                $unitIds   = $request->unit[$detail->alat_id] ?? [];
                $unitIds   = array_map('intval', $unitIds);

                // Jumlah unit yang dipilih harus sama dengan jumlah yang diajukan
                if (count($unitIds) !== (int) $detail->jumlah) {
                    $alat = $detail->alat;
                    throw new \Exception("Jumlah unit yang dipilih untuk alat '{$alat->nama_alat}' (#{count($unitIds)}) harus sama dengan jumlah pinjam ({$detail->jumlah}).");
                }

                // Pastikan semua unit milik alat yang benar DAN berstatus tersedia (lock untuk antisipasi race condition)
                $units = \App\Models\UnitAlat::whereIn('id', $unitIds)
                    ->where('alat_id', $detail->alat_id)
                    ->where('status', 'tersedia')
                    ->lockForUpdate()
                    ->get();

                if ($units->count() !== count($unitIds)) {
                    $alat = $detail->alat;
                    throw new \Exception("Beberapa unit untuk alat '{$alat->nama_alat}' tidak tersedia atau bukan milik alat tersebut. Silakan pilih ulang.");
                }

                // Tandai unit sebagai dipinjam dan masukkan ke pivot detail_pinjam_unit
                foreach ($units as $unit) {
                    $unit->update(['status' => 'dipinjam']);
                    $detail->unitAlat()->attach($unit->id, ['kondisi_keluar' => $unit->kondisi]);
                }
            }

            // Ubah status menjadi dipinjam
            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();
            return redirect()->route('petugas.peminjaman.index')->with('success', 'Peminjaman disetujui dan unit alat berhasil dialokasikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'unit_kondisi'      =>  'required|array',
            'unit_kondisi.*'    =>  'required|in:baik,rusak',
            'denda'             =>  'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam.alat', 'detailPinjam.unitAlat')->lockForUpdate()->findOrFail($peminjamanId);

            // Pastikan semua unit yang dialokasikan pada peminjaman memiliki kondisi (SET exact match)
            $unitTerdaftar = array_map(
                'strval',
                $peminjaman->detailPinjam
                    ->flatMap(fn($detail) => $detail->unitAlat->pluck('id'))
                    ->all()
            );

            $unitDikirim = array_map(
                'strval',
                array_keys($request->unit_kondisi)
            );

            sort($unitTerdaftar);
            sort($unitDikirim);

            if ($unitTerdaftar !== $unitDikirim) {
                throw new \Exception('Data unit yang dikembalikan tidak valid. Mohon pastikan semua unit yang dipinjam telah ditentukan kondisinya dengan benar.');
            }

            // Validasi Denda Kerusakan: wajib jika ada unit rusak, nol jika semua baik
            if (in_array('rusak', $request->unit_kondisi, true)) {
                if ($request->denda === null || (int)$request->denda < 1000) {
                    throw new \Exception('Denda kerusakan wajib diisi minimal Rp1.000 jika ada unit yang rusak.');
                }
            } else {
                $request->merge(['denda' => 0]);
            }

            // Pastikan peminjaman masih aktif dan bisa diproses (guard alur pengembalian resmi)
            if (!$peminjaman->canBeReturned()) {
                throw new \Exception("Peminjaman ini berstatus '{$peminjaman->status}' dan tidak dapat diproses untuk pengembalian.");
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

            // Denda kerusakan dimasukkan Petugas secara manual (divalidasi di atas)
            $dendaKerusakan = (int)$request->denda;

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            // Tentukan kondisi pengembalian secara keseluruhan dari kondisi unit
            $kondisiKembali = in_array('rusak', $request->unit_kondisi, true) ? 'rusak' : 'baik';

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id'         =>  $peminjaman->id,
                'tgl_kembali'           =>  $tglKembali,
                'kondisi_kembali'       =>  $kondisiKembali,
                'denda_keterlambatan'   =>  $dendaKeterlambatan,
                'denda_kerusakan'       =>  $dendaKerusakan,
                'denda'                 =>  $totalDenda,
                'petugas_id'            =>  auth()->id(),
            ]);

            // Kembalikan stok unit alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                // Ambil unit yang dipinjam untuk detail ini
                $units = $detail->unitAlat;

                $jmlRusak = 0;

                // Update kondisi masing-masing unit berdasarkan nomor seri
                foreach ($units as $unit) {
                    $kondisi = $request->unit_kondisi[$unit->id] ?? 'baik';

                    if ($kondisi === 'rusak') {
                        $unit->update(['status' => 'rusak', 'kondisi' => 'rusak']);
                        $detail->unitAlat()->updateExistingPivot($unit->id, ['kondisi_masuk' => 'rusak']);
                        $jmlRusak++;
                    } else {
                        $unit->update(['status' => 'tersedia', 'kondisi' => 'baik']);
                        $detail->unitAlat()->updateExistingPivot($unit->id, ['kondisi_masuk' => 'baik']);
                    }
                }

                // Update info di detail_pinjam
                $detail->update(['jumlah_rusak' => $jmlRusak]);

                if ($jmlRusak > 0 && $alat) {
                    \App\Services\NotifikasiService::alatRusakBaru($alat, $jmlRusak);
                }
            }

            // Selesaikan notifikasi "Permintaan Pengembalian" yang sudah tidak relevan
            \App\Services\NotifikasiService::selesaikanPermintaanPengembalian($peminjaman);

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
        // "telat" adalah status virtual (status DB = 'dipinjam' + tgl_kembali_plan lewat),
        // jadi cukup query 'dipinjam' saja — accessor akan menampilkannya sebagai 'telat'.
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->where('status', 'dipinjam')
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

    public function indexLaporan(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'dari'      => ['nullable', 'date', 'date_format:Y-m-d'],
            'sampai'    => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:dari'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Filter tanggal tidak valid.');
        }

        $dari   = $request->input('dari');
        $sampai = $request->input('sampai');

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
