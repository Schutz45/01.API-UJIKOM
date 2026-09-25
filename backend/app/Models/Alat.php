<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'stok',
        'stok_rusak',
        'deskripsi',
        'gambar'
    ];

    protected function casts(): array {
        return ['stok' => 'integer', 'stok_rusak' => 'integer',];
    }

    public function kategori(): BelongsTo {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPinjam(): HasMany {
        return $this->hasMany(DetailPinjam::class);
    }

    /**
     * KATALOG PEMINJAM: alat masih bisa dipinjam selama stok tersedia > 0,
     * meskipun sebagian unit rusak.
     */
    public function scopeTersedia($query) {
        return $query->where('stok', '>', 0);
    }

    public function scopeRusak($query) {
        return $query->where('stok_rusak', '>', 0);
    }

    /**
     * Alat yang membutuhkan perhatian admin: ada unit rusak.
     */
    public function scopePerluPerhatian($query) {
        return $query->where('stok_rusak', '>', 0);
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
        if ($this->stok > 0 && $this->stok_rusak > 0) {
            return 'sebagian_rusak';
        }
        if ($this->stok > 0) {
            return 'baik';
        }
        if ($this->stok_rusak > 0) {
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

    /**
     * Sinkronisasi kolom status_kondisi di database agar konsisten
     * dengan nilai computed. Dipanggil setelah setiap perubahan stok.
     */
    public function syncStatusKondisi(): void
    {
        $this->status_kondisi = $this->status_kondisi;
        $this->saveQuietly();
    }
}
