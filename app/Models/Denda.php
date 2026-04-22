<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denda extends Model
{
    protected $table = 'dendas';

    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'jenis_denda',
        'jumlah_hari',
        'nominal',
        'status_bayar',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
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
