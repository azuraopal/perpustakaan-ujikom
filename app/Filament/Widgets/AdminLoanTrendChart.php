<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Widgets\ChartWidget;

class AdminLoanTrendChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Tren Pengajuan vs Pengembalian (6 Bulan)';

    protected ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $offset) => now()->subMonths($offset));

        $labels = $months
            ->map(fn ($month) => $month->format('M Y'))
            ->all();

        $pengajuan = $months
            ->map(function ($month): int {
                return Peminjaman::query()
                    ->whereBetween('created_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count();
            })
            ->all();

        $pengembalian = $months
            ->map(function ($month): int {
                return Peminjaman::query()
                    ->whereNotNull('tanggal_dikembalikan')
                    ->whereBetween('tanggal_dikembalikan', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count();
            })
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan',
                    'data' => $pengajuan,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.12)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
                [
                    'label' => 'Pengembalian',
                    'data' => $pengembalian,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.10)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): ?array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
