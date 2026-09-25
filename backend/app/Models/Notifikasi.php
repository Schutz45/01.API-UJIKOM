<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'jenis',
        'judul',
        'pesan',
        'dibaca',
        'referensi_tipe',
        'referensi_id',
        'url_tujuan',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |----------------------------------------------------------------------
    | Scope: notifikasi yang belum dibaca untuk user tertentu
    |----------------------------------------------------------------------
    */
    public function scopeBelumDibaca($query, $userId = null)
    {
        return $query->where('dibaca', false)
            ->when($userId, fn ($q) => $q->where('user_id', $userId));
    }

    /*
    |----------------------------------------------------------------------
    | Helper: kirim notifikasi baru (factory method)
    |----------------------------------------------------------------------
    */
    public static function kirim(
        int $userId,
        string $jenis,
        string $judul,
        string $pesan,
        ?string $referensiTipe = null,
        ?int $referensiId = null,
        ?string $urlTujuan = null
    ): self {
        return self::create([
            'user_id'         => $userId,
            'jenis'           => $jenis,
            'judul'           => $judul,
            'pesan'           => $pesan,
            'dibaca'          => false,
            'referensi_tipe'  => $referensiTipe,
            'referensi_id'    => $referensiId,
            'url_tujuan'      => $urlTujuan,
        ]);
    }
}
