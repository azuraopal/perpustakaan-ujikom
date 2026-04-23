<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    public const STATUS_MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DIPINJAM = 'dipinjam';
    public const STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN = 'menunggu_verifikasi_pengembalian';
    public const STATUS_DIKEMBALIKAN = 'dikembalikan';
    public const STATUS_TERLAMBAT = 'terlambat';

    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'buku_id',
        'disetujui_oleh',
        'disetujui_pada',
        'ditolak_oleh',
        'ditolak_pada',
        'alasan_penolakan',
        'tanggal_pinjam',
        'tanggal_harus_kembali',
        'tanggal_dikembalikan',
        'pengembalian_diajukan_pada',
        'pengembalian_diverifikasi_oleh',
        'pengembalian_diverifikasi_pada',
        'jumlah',
        'status',
        'kondisi_buku',
        'catatan',
        'catatan_pengembalian',
    ];

    protected function casts(): array
    {
        return [
            'disetujui_pada' => 'datetime',
            'ditolak_pada' => 'datetime',
            'tanggal_pinjam' => 'date',
            'tanggal_harus_kembali' => 'date',
            'tanggal_dikembalikan' => 'date',
            'pengembalian_diajukan_pada' => 'datetime',
            'pengembalian_diverifikasi_pada' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_MENUNGGU_PERSETUJUAN => 'Menunggu Persetujuan',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_DIPINJAM => 'Dipinjam',
            self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN => 'Menunggu Verifikasi Pengembalian',
            self::STATUS_DIKEMBALIKAN => 'Dikembalikan',
            self::STATUS_TERLAMBAT => 'Terlambat',
        ];
    }

    public static function statusLabel(string $status): string
    {
        return static::statusOptions()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_MENUNGGU_PERSETUJUAN => 'info',
            self::STATUS_DITOLAK => 'danger',
            self::STATUS_DIPINJAM => 'warning',
            self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN => 'primary',
            self::STATUS_DIKEMBALIKAN => 'success',
            self::STATUS_TERLAMBAT => 'danger',
            default => 'gray',
        };
    }

    public static function activeLoanStatuses(): array
    {
        return [
            self::STATUS_MENUNGGU_PERSETUJUAN,
            self::STATUS_DIPINJAM,
            self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN,
            self::STATUS_TERLAMBAT,
        ];
    }

    public function getDisplayStatusAttribute(): string
    {
        if (
            ($this->status === self::STATUS_DIPINJAM)
            && blank($this->tanggal_dikembalikan)
            && filled($this->tanggal_harus_kembali)
            && $this->tanggal_harus_kembali->isPast()
        ) {
            return self::STATUS_TERLAMBAT;
        }

        return $this->status;
    }

    public static function generateKode(): string
    {
        $date = now()->format('Ymd');
        $lastRecord = static::where('kode_peminjaman', 'like', "PJM-{$date}-%")
            ->orderByDesc('kode_peminjaman')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->kode_peminjaman, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PJM-{$date}-{$newNumber}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    public function returnVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengembalian_diverifikasi_oleh');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function dendas(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
