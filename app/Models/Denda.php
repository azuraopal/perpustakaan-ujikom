<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denda extends Model
{
    protected $table = 'dendas';

    public const STATUS_BELUM_BAYAR = 'belum_bayar';
    public const STATUS_SUDAH_BAYAR = 'sudah_bayar';

    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'jenis_denda',
        'jumlah_hari',
        'nominal',
        'status_bayar',
        'metode_pembayaran',
        'midtrans_snap_token',
        'midtrans_order_id',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
    }

    public static function statusBayarLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_BELUM_BAYAR => 'Belum Lunas',
            self::STATUS_SUDAH_BAYAR => 'Sudah Lunas',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function statusBayarColor(string $status): string
    {
        return match ($status) {
            self::STATUS_BELUM_BAYAR => 'danger',
            self::STATUS_SUDAH_BAYAR => 'success',
            default => 'gray',
        };
    }

    public static function jenisDendaLabel(string $jenis): string
    {
        return match ($jenis) {
            'keterlambatan' => 'Keterlambatan',
            'kerusakan' => 'Kerusakan',
            'kehilangan' => 'Kehilangan',
            default => ucfirst($jenis),
        };
    }

    public static function metodePembayaranLabel(?string $metode): string
    {
        return match ($metode) {
            'cash' => 'Tunai',
            'midtrans' => 'Online (Midtrans)',
            default => '-',
        };
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
