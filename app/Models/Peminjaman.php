<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_harus_kembali',
        'tanggal_dikembalikan',
        'jumlah',
        'status',
        'kondisi_buku',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_harus_kembali' => 'date',
            'tanggal_dikembalikan' => 'date',
        ];
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

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function dendas(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
