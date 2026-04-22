<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'bukus';

    protected $fillable = [
        'kategori_id',
        'rak_buku_id',
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'sinopsis',
        'cover_image',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function rakBuku(): BelongsTo
    {
        return $this->belongsTo(RakBuku::class);
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
}
