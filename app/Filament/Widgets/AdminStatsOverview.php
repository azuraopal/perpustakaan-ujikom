<?php

namespace App\Filament\Widgets;

use App\Models\Buku;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBuku = (int) Buku::count();
        $totalStok = (int) Buku::sum('stok');
        $pinjamanAktif = (int) Peminjaman::query()
            ->whereIn('status', Peminjaman::activeLoanStatuses())
            ->count();
        $dendaBelumBayar = (float) Denda::query()
            ->where('status_bayar', 'belum_bayar')
            ->sum('nominal');
        $totalSiswa = (int) User::where('role', 'siswa')->where('is_active', true)->count();

        $pengajuanTrend = collect(range(6, 0))->map(fn ($d) =>
            Peminjaman::whereDate('created_at', now()->subDays($d)->toDateString())->count()
        )->all();

        $pengembalianTrend = collect(range(6, 0))->map(fn ($d) =>
            Peminjaman::where('status', Peminjaman::STATUS_DIKEMBALIKAN)
                ->whereDate('tanggal_dikembalikan', now()->subDays($d)->toDateString())->count()
        )->all();

        return [
            Stat::make('Total Judul Buku', number_format($totalBuku))
                ->description('Katalog perpustakaan')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('info')
                ->chart($pengajuanTrend),
            Stat::make('Total Stok Tersedia', number_format($totalStok))
                ->description('Seluruh eksemplar')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('success')
                ->chart($pengembalianTrend),
            Stat::make('Pinjaman Aktif', number_format($pinjamanAktif))
                ->description('Menunggu / dipinjam / verifikasi')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart($pengajuanTrend),
            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($dendaBelumBayar, 0, ',', '.'))
                ->description('Tagihan denda siswa')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('danger')
                ->chart(array_reverse($pengajuanTrend)),
        ];
    }
}
