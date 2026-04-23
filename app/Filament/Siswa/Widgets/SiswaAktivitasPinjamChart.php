<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Denda;
use App\Models\Peminjaman;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SiswaAktivitasPinjamChart extends ChartWidget
{
    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Aktivitas Pengajuan Anda (6 Bulan)';

    protected ?string $maxHeight = '180px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $userId = Auth::id();
        $months = collect(range(5, 0))->map(fn (int $offset) => now()->subMonths($offset));

        $labels = $months
            ->map(fn ($month) => $month->format('M Y'))
            ->all();

        if (! $userId) {
            return [
                'datasets' => [
                    [
                        'label' => 'Pengajuan',
                        'data' => array_fill(0, count($labels), 0),
                        'backgroundColor' => 'rgba(14, 165, 233, 0.7)',
                    ],
                    [
                        'label' => 'Denda',
                        'data' => array_fill(0, count($labels), 0),
                        'backgroundColor' => 'rgba(244, 63, 94, 0.7)',
                    ],
                ],
                'labels' => $labels,
            ];
        }

        $pengajuan = $months
            ->map(function ($month) use ($userId): int {
                return Peminjaman::query()
                    ->where('user_id', $userId)
                    ->whereBetween('created_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count();
            })
            ->all();

        $denda = $months
            ->map(function ($month) use ($userId): int {
                return Denda::query()
                    ->where('user_id', $userId)
                    ->whereBetween('created_at', [
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
                    'backgroundColor' => 'rgba(14, 165, 233, 0.7)',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Denda',
                    'data' => $denda,
                    'backgroundColor' => 'rgba(244, 63, 94, 0.7)',
                    'borderRadius' => 6,
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
