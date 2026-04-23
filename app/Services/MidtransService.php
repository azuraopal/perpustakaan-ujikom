<?php

namespace App\Services;

use App\Models\Denda;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(Denda $denda): string
    {
        $orderId = 'DENDA-' . $denda->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $denda->nominal,
            ],
            'customer_details' => [
                'first_name' => $denda->user->nama_lengkap ?? 'Siswa',
                'email' => $denda->user->email ?? 'siswa@sekolah.sch.id',
            ],
            'item_details' => [
                [
                    'id' => 'denda-' . $denda->id,
                    'price' => (int) $denda->nominal,
                    'quantity' => 1,
                    'name' => 'Denda ' . Denda::jenisDendaLabel($denda->jenis_denda) . ' - ' . ($denda->peminjaman->kode_peminjaman ?? ''),
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $denda->update([
            'midtrans_snap_token' => $snapToken,
            'midtrans_order_id' => $orderId,
        ]);

        return $snapToken;
    }

    public static function getClientKey(): string
    {
        return config('services.midtrans.client_key');
    }

    public static function isProduction(): bool
    {
        return config('services.midtrans.is_production', false);
    }
}
