<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnitAlat extends Model
{
    protected $table = 'unit_alat';

    protected $fillable = [
        'alat_id',
        'nomor_seri',
        'status',
        'kondisi'
    ];

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    public function detailPinjam(): BelongsToMany
    {
        return $this->belongsToMany(DetailPinjam::class, 'detail_pinjam_unit')
                    ->withPivot('kondisi_keluar', 'kondisi_masuk')
                    ->withTimestamps();
    }
}
