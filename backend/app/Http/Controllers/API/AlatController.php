<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //  Tetap menggunakan eager loading untuk mencegah N+1 Query
        $alat = Alat::with('kategori')->latest()->get();
        return response()->json([
            'message'   =>  'Daftar alat berhasil diambil.',
            'data'      =>  AlatResource::collection($alat)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlatRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['stok_rusak'] = $data['stok_rusak'] ?? 0;

        $alat = DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('gambar')) {
                $data['gambar'] =   $request->file('gambar')->store('alat', 'public');
            }
            return Alat::create($data);
        });
        return response()->json([
            'message'   =>  'Alat berhasil ditambahkan.',
            'data'      =>  new AlatResource($alat->load('kategori'))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alat $alat): JsonResponse
    {
        // Menggunakan Route Model binding ($alat) dikombinasikan dengan load()
        return response()->json([
            'data'  =>  new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlatRequest $request, Alat $alat): JsonResponse
    {
        $data       =   $request->validated();
        $oldGambar  =   $alat->gambar; // Simpan path gambar lama lebih daulu

        // Stok & stok_rusak tidak boleh diubah melalui endpoint update ini.
        // Perubahan unit hanya melalui endpoint tandai-rusak / perbaiki.

        DB::transaction(function () use ($request, &$data, $alat, $oldGambar){
            if ($request->hasFile('gambar')) {
                $data['gambar'] =   $request->file('gambar')->store('alat', 'public');

                // Hapus gambar lama dari server HANYA setelah gambar baru sukses masuk database
                if ($oldGambar) {
                    Storage::disk('public')->delete($oldGambar);
                }
            }
            $alat->update($data);
        });
        return response()->json([
            'message'   =>  'Alat berhasil diperbarui.',
            'data'      =>  new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat): JsonResponse
    {
        if ($alat->detailPinjam()->exists()) {
            return response()->json([
                'message' => 'Alat tidak dapat dihapus karena memiliki riwayat peminjaman.'
            ], 422);
        }

        DB::transaction(function () use ($alat) {
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $alat->delete();
        });
        return response()->json([
            'message'   =>  'Alat berhasil dihapus.'
        ]);
    }

    public function katalog(): JsonResponse
    {
        $alat = Alat::with('kategori')->tersedia()->latest()->get();
        return response()->json([
            'message'   =>  'Katalog alat tersedia.',
            'data'      =>  AlatResource::collection($alat)
        ]);
    }

    /**
     * Tandai sejumlah unit alat sebagai rusak.
     * Stok (baik) berkurang, stok_rusak bertambah.
     */
    public function tandaiRusak(Request $request, Alat $alat): JsonResponse
    {
        // Validasi stok
        $request->validate([
            'jumlah'    =>  ['required', 'integer', 'min:1', "max:{$alat->jumlah_tersedia}"],
        ], [
            'jumlah.required'  => 'Jumlah unit yang ditandai rusak wajib diisi.',
            'jumlah.min'       => 'Minimal 1 unit harus ditandai rusak.',
            'jumlah.max'       => "Jumlah melebihi stok baik yang tersedia ({$alat->jumlah_tersedia} unit).",
        ]);

        try {
            $alat->markUnitsAsBroken((int)$request->jumlah);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $alat->refresh();

        return response()->json([
            'message'   =>  "Berhasil menandai {$request->jumlah} unit '{$alat->nama_alat}' sebagai rusak.",
            'data'      =>  new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * Perbaiki sejumlah unit alat yang rusak.
     * Stok_rusak berkurang, stok (baik) bertambah.
     */
    public function perbaiki(Request $request, Alat $alat): JsonResponse
    {
        // Validasi
        $request->validate([
            'jumlah'    =>  ['required', 'integer', 'min:1', "max:{$alat->jumlah_rusak}"],
        ], [
            'jumlah.required'  => 'Jumlah unit yang diperbaiki wajib diisi.',
            'jumlah.min'       => 'Minimal 1 unit harus diperbaiki.',
            'jumlah.max'       => "Jumlah melebihi stok rusak yang tersedia ({$alat->jumlah_rusak} unit).",
        ]);

        try {
            $alat->repairUnits((int)$request->jumlah);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $alat->refresh();

        return response()->json([
            'message'   =>  "Berhasil memperbaiki {$request->jumlah} unit '{$alat->nama_alat}'.",
            'data'      =>  new AlatResource($alat->load('kategori'))
        ]);
    }
}
