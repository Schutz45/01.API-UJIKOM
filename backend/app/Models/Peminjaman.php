<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
        'permintaan_pengembalian',
    ];

    protected function casts(): array {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne {
        return $this->hasOne(Pengembalian::class);
    }

    /**
     * Guard Aturan Bisnis Transisi Status
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        $allowedTransitions = [
            'diajukan'     => ['dipinjam', 'ditolak'],
            'dipinjam'     => ['telat', 'dikembalikan'],
            'telat'        => ['dikembalikan'],
            'dikembalikan' => [], // Status terminal - terkunci permanen
            'ditolak'      => [], // Status terminal - terkunci permanen
        ];

        $currentStatus = $this->status;

        // Mencegah perubahan jika status sudah sama (cegah eksekusi ulang)
        if ($currentStatus === $targetStatus) {
            return false;
        }

        return in_array($targetStatus, $allowedTransitions[$currentStatus] ?? [], true);
    }
}
