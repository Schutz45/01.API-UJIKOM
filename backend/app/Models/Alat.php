<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'deskripsi',
        'gambar'
    ];

    protected function casts(): array {
        return [];
    }

    public function kategori(): BelongsTo {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPinjam(): HasMany {
        return $this->hasMany(DetailPinjam::class);
    }

    public function unitAlat(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(UnitAlat::class);
    }

    /**
     * Menandai unit-unit tertentu sebagai rusak.
     */
    public function markUnitsAsBroken(array $unitIds): void
    {
        DB::transaction(function () use ($unitIds) {
            $units = $this->unitAlat()
                ->whereIn('id', $unitIds)
                ->where('status', 'tersedia')
                ->lockForUpdate()
                ->get();

            if ($units->count() !== count($unitIds)) {
                throw new \Exception("Beberapa unit tidak ditemukan atau tidak tersedia untuk ditandai rusak.");
            }

            foreach ($units as $unit) {
                $unit->update(['status' => 'rusak', 'kondisi' => 'rusak']);
            }
        });
    }

    /**
     * Memperbaiki unit-unit tertentu yang rusak.
     */
    public function repairUnits(array $unitIds): void
    {
        DB::transaction(function () use ($unitIds) {
            $units = $this->unitAlat()
                ->whereIn('id', $unitIds)
                ->where('kondisi', 'rusak')
                ->lockForUpdate()
                ->get();

            if ($units->count() !== count($unitIds)) {
                throw new \Exception("Beberapa unit tidak ditemukan atau tidak dalam kondisi rusak untuk diperbaiki.");
            }

            foreach ($units as $unit) {
                $unit->update(['status' => 'tersedia', 'kondisi' => 'baik']);
            }
        });
    }

    // Accessor Computed Stok (Ganti nama agar tidak konflik dengan kolom)
    public function getJumlahTersediaAttribute(): int {
        return $this->unitAlat()->where('status', 'tersedia')->count();
    }

    // Accessor Computed Stok Rusak
    public function getJumlahRusakAttribute(): int {
        return $this->unitAlat()->where('kondisi', 'rusak')->count();
    }

    /**
     * KATALOG PEMINJAM: alat masih bisa dipinjam selama unit tersedia > 0
     */
    public function scopeTersedia($query) {
        return $query->whereHas('unitAlat', function($q) {
            $q->where('status', 'tersedia');
        });
    }

    public function scopeRusak($query) {
        return $query->whereHas('unitAlat', function($q) {
            $q->where('kondisi', 'rusak');
        });
    }

    /**
     * Alat yang membutuhkan perhatian admin: ada unit rusak.
     */
    public function scopePerluPerhatian($query) {
        return $query->whereHas('unitAlat', function($q) {
            $q->where('kondisi', 'rusak');
        });
    }

    /**
     * STATUS KONDISI OTOMATIS (Computed / Accessor).
     * Sumber kebenaran tunggal: stok & stok_rusak.
     *
     * - stok > 0  & stok_rusak == 0 -> 'baik'
     * - stok > 0  & stok_rusak > 0  -> 'sebagian_rusak'
     * - stok == 0 & stok_rusak > 0  -> 'rusak'
     * - stok == 0 & stok_rusak == 0 -> 'habis'
     */
    public function getStatusKondisiAttribute(): string
    {
        $tersedia = $this->jumlah_tersedia;
        $rusak = $this->jumlah_rusak;

        if ($tersedia > 0 && $rusak > 0) {
            return 'sebagian_rusak';
        }
        if ($tersedia > 0) {
            return 'baik';
        }
        if ($rusak > 0) {
            return 'rusak';
        }
        return 'habis';
    }

    /**
     * Label tampilan untuk status_kondisi (digunakan di seluruh view).
     */
    public function getStatusKondisiLabelAttribute(): string
    {
        return match ($this->status_kondisi) {
            'baik'            => 'Baik',
            'sebagian_rusak'  => 'Sebagian Rusak',
            'rusak'           => 'Rusak',
            default           => 'Habis',
        };
    }

    // Helper backward compatibility
    public function getStokAttribute(): int {
        return $this->jumlah_tersedia;
    }

    public function getStokRusakAttribute(): int {
        return $this->jumlah_rusak;
    }
}
