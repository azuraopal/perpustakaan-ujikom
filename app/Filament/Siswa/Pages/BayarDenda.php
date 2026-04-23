<?php

namespace App\Filament\Siswa\Pages;

use App\Models\Denda;
use App\Services\MidtransService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BayarDenda extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Bayar Denda';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Pembayaran Denda';
    protected static ?string $slug = 'bayar-denda';
    protected string $view = 'filament.siswa.pages.bayar-denda';

    public static function shouldRegisterNavigation(): bool
    {
        $userId = Auth::id();
        if (! $userId) {
            return false;
        }
        return Denda::where('user_id', $userId)->where('status_bayar', Denda::STATUS_BELUM_BAYAR)->exists();
    }

    public function getDendas()
    {
        return Denda::with('peminjaman')
            ->where('user_id', Auth::id())
            ->where('status_bayar', Denda::STATUS_BELUM_BAYAR)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function bayarMidtrans(int $dendaId): void
    {
        $denda = Denda::where('id', $dendaId)
            ->where('user_id', Auth::id())
            ->where('status_bayar', Denda::STATUS_BELUM_BAYAR)
            ->firstOrFail();

        try {
            $service = new MidtransService();
            $snapToken = $service->createSnapToken($denda);

            $this->dispatch('open-snap', snapToken: $snapToken, dendaId: $denda->id);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Membuat Pembayaran')
                ->body('Terjadi kesalahan saat menghubungi Midtrans: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function tandaiLunas(int $dendaId): void
    {
        $denda = Denda::where('id', $dendaId)
            ->where('user_id', Auth::id())
            ->where('status_bayar', Denda::STATUS_BELUM_BAYAR)
            ->firstOrFail();

        $denda->update([
            'status_bayar' => Denda::STATUS_SUDAH_BAYAR,
            'metode_pembayaran' => 'midtrans',
            'tanggal_bayar' => now()->toDateString(),
        ]);

        Notification::make()
            ->title('Pembayaran Berhasil!')
            ->body('Denda telah dilunasi.')
            ->success()
            ->send();
    }

    public function getClientKey(): string
    {
        return MidtransService::getClientKey();
    }

    public function getSnapUrl(): string
    {
        return MidtransService::isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }
}
