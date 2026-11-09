<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogAktivitasResource;
use App\Models\LogAktivitas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogAktivitasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'role'       => ['nullable', 'string', 'in:admin,petugas,peminjam'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi parameter gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $query = LogAktivitas::with('user');

        // Filter Role User
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Filter Tanggal
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->start_date);
        });
        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->end_date);
        });

        $logs = $query->latest()->get();

        return response()->json([
            'message'       =>  'Catatan log aktivitas berhasil diambil.',
            'total_data'    =>  $logs->count(),
            'data'          =>  LogAktivitasResource::collection($logs)
        ]);
    }
}
