<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
    }

    public function handle(Request $request)
    {
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status ?? 'accept';

        $denda = Denda::where('midtrans_order_id', $orderId)->first();

        if (! $denda) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $isPaid = false;

        if ($transactionStatus === 'capture') {
            $isPaid = ($fraudStatus === 'accept');
        } elseif (in_array($transactionStatus, ['settlement'])) {
            $isPaid = true;
        }

        if ($isPaid) {
            $denda->update([
                'status_bayar' => Denda::STATUS_SUDAH_BAYAR,
                'metode_pembayaran' => 'midtrans',
                'tanggal_bayar' => now()->toDateString(),
            ]);

            $kodePeminjaman = $denda->peminjaman->kode_peminjaman ?? '-';
            $nominalFormatted = number_format($denda->nominal, 0, ',', '.');

            // Notify siswa
            \Filament\Notifications\Notification::make()
                ->title('Denda Lunas')
                ->body("Pembayaran denda Rp {$nominalFormatted} untuk kode {$kodePeminjaman} telah dikonfirmasi otomatis via Midtrans.")
                ->success()
                ->sendToDatabase($denda->user);

            // Notify all admins
            $admins = \App\Models\User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Pembayaran Denda Online')
                    ->body("{$denda->user->nama_lengkap} melunasi denda Rp {$nominalFormatted} via Midtrans untuk kode {$kodePeminjaman}.")
                    ->success()
                    ->sendToDatabase($admins);
            }

            // Log activity
            \App\Models\LogAktivitas::create([
                'user_id' => $denda->user_id,
                'aktivitas' => 'Pembayaran Denda Online (Webhook)',
                'detail' => "Denda Rp {$nominalFormatted} untuk kode {$kodePeminjaman} dikonfirmasi otomatis oleh Midtrans.",
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
