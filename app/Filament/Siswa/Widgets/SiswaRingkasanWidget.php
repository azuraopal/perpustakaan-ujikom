<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SiswaRingkasanWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $userId = Auth::id();
        $maxPinjam = (int) Pengaturan::getValue('max_peminjaman', '3');

        $pinjamanAktif = 0;
        $menungguPersetujuan = 0;
        $dendaBelumBayar = 0.0;

        $pinjamTrend = array_fill(0, 7, 0);
        $dendaTrend = array_fill(0, 7, 0);

        if ($userId) {
            $pinjamanAktif = (int) Peminjaman::query()
                ->where('user_id', $userId)
                ->whereIn('status', Peminjaman::activeLoanStatuses())
                ->sum('jumlah');

            $menungguPersetujuan = (int) Peminjaman::query()
                ->where('user_id', $userId)
                ->where('status', Peminjaman::STATUS_MENUNGGU_PERSETUJUAN)
                ->count();

            $dendaBelumBayar = (float) Denda::query()
                ->where('user_id', $userId)
                ->where('status_bayar', 'belum_bayar')
                ->sum('nominal');

            $pinjamTrend = collect(range(6, 0))->map(fn ($d) =>
                Peminjaman::where('user_id', $userId)
                    ->whereDate('created_at', now()->subDays($d)->toDateString())->count()
            )->all();

            $dendaTrend = collect(range(6, 0))->map(fn ($d) =>
                Denda::where('user_id', $userId)
                    ->whereDate('created_at', now()->subDays($d)->toDateString())->count()
            )->all();
        }

        $sisaKuota = max(0, $maxPinjam - $pinjamanAktif);

        return [
            Stat::make('Kuota Tersisa', $sisaKuota . ' / ' . $maxPinjam)
                ->description('Buku yang masih bisa dipinjam')
                ->descriptionIcon('heroicon-o-scale')
                ->color($sisaKuota > 0 ? 'success' : 'danger')
                ->chart($pinjamTrend),
            Stat::make('Pinjaman Aktif', number_format($pinjamanAktif))
                ->description('Sedang dipinjam / verifikasi')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('warning')
                ->chart($pinjamTrend),
            Stat::make('Menunggu Persetujuan', number_format($menungguPersetujuan))
                ->description('Belum diproses admin')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info')
                ->chart(array_reverse($pinjamTrend)),
            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($dendaBelumBayar, 0, ',', '.'))
                ->description('Tagihan terbuka')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('danger')
                ->chart($dendaTrend),
        ];
    }
}
