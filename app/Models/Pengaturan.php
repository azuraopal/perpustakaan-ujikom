<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturans';

    protected $fillable = [
        'kunci',
        'nilai',
        'deskripsi',
    ];

    public static function getValue(string $kunci, string $default = ''): string
    {
        return static::where('kunci', $kunci)->value('nilai') ?? $default;
    }
}
