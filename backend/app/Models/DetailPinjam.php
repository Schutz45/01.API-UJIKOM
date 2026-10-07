<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPinjam extends Model
{
    protected $table = 'detail_pinjam';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
        'jumlah_rusak'
    ];

    protected function casts(): array {
        return ['jumlah' => 'integer', 'jumlah_rusak' => 'integer'];
    }

    public function peminjaman(): BelongsTo {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat(): BelongsTo {
        return $this->belongsTo(Alat::class);
    }

    public function unitAlat(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(UnitAlat::class, 'detail_pinjam_unit')
                    ->withPivot('kondisi_keluar', 'kondisi_masuk')
                    ->withTimestamps();
    }
}
