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

    /**
     * Virtual Status: Menghitung status 'telat' secara dinamis.
     * Status di DB tetap 'dipinjam', tapi secara tampilan/logic bisnis dianggap 'telat'.
     */
    public function getStatusAttribute($value)
    {
        if ($value === 'dipinjam' && $this->tgl_kembali_plan && $this->tgl_kembali_plan->isPast() && !$this->tgl_kembali_plan->isToday()) {
            return 'telat';
        }
        return $value;
    }

    /**
     * Scope untuk peminjaman yang sedang berjalan (dipinjam ATAU telat secara dinamis)
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Scope khusus untuk yang sudah melewati tanggal kembali
     */
    public function scopeTelat($query)
    {
        return $query->where('status', 'dipinjam')
                     ->whereDate('tgl_kembali_plan', '<', now()->toDateString());
    }

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
            'dipinjam'     => [], // Tidak bisa langsung ke dikembalikan
            'telat'        => [], // Tidak bisa langsung ke dikembalikan
            'dikembalikan' => [], // Status terminal
            'ditolak'      => [], // Status terminal
        ];

        $currentStatus = $this->status; // Menggunakan accessor getStatusAttribute

        // Mencegah perubahan jika status sudah sama (cegah eksekusi ulang)
        if ($currentStatus === $targetStatus) {
            return false;
        }

        return in_array($targetStatus, $allowedTransitions[$currentStatus] ?? [], true);
    }

    /**
     * Khusus untuk alur pengembalian resmi (bukan ubah status manual).
     * Pengembalian hanya bisa dilakukan jika peminjaman sedang aktif
     * (dipinjam atau telat), dan belum pernah dikembalikan sebelumnya.
     */
    public function canBeReturned(): bool
    {
        return in_array($this->status, ['dipinjam', 'telat'])
            && !$this->pengembalian()->exists();
    }
}
