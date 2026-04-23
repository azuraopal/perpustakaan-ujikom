<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'kunci' => 'denda_rusak_ringan',
                'nilai' => '5000',
                'deskripsi' => 'Denda untuk buku rusak ringan',
            ],
            [
                'kunci' => 'denda_rusak_berat',
                'nilai' => '15000',
                'deskripsi' => 'Denda untuk buku rusak berat',
            ],
            [
                'kunci' => 'denda_hilang',
                'nilai' => '50000',
                'deskripsi' => 'Denda untuk buku hilang',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('pengaturans')->updateOrInsert(
                ['kunci' => $setting['kunci']],
                [
                    'nilai' => $setting['nilai'],
                    'deskripsi' => $setting['deskripsi'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('pengaturans')->whereIn('kunci', [
            'denda_rusak_ringan',
            'denda_rusak_berat',
            'denda_hilang',
        ])->delete();
    }
};
