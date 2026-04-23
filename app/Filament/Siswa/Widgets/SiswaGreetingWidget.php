<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SiswaGreetingWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.siswa.widgets.greeting-widget';

    public function getGreeting(): string
    {
        $hour = (int) now()->format('H');

        return match (true) {
            $hour < 12 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    }

    public function getUserName(): string
    {
        return Auth::user()?->nama_lengkap ?? 'Siswa';
    }

    public function getKuota(): int
    {
        $userId = Auth::id();
        if (! $userId) {
            return 0;
        }

        $max = (int) Pengaturan::getValue('max_peminjaman', '3');
        $aktif = (int) Peminjaman::query()
            ->where('user_id', $userId)
            ->whereIn('status', Peminjaman::activeLoanStatuses())
            ->sum('jumlah');

        return max(0, $max - $aktif);
    }

    public function getDateString(): string
    {
        return now()->translatedFormat('l, d F Y');
    }
}
