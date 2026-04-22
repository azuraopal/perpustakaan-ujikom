<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RakBuku extends Model
{
    protected $table = 'rak_bukus';

    protected $fillable = [
        'kode_rak',
        'nama',
        'lokasi',
        'kapasitas',
    ];

    public function bukus(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}
