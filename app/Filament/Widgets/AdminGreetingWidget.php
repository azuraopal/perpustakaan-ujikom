<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AdminGreetingWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.admin.widgets.greeting-widget';

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
        return Auth::user()?->nama_lengkap ?? 'Admin';
    }

    public function getPendingCount(): int
    {
        return (int) Peminjaman::where('status', Peminjaman::STATUS_MENUNGGU_PERSETUJUAN)->count();
    }

    public function getDateString(): string
    {
        return now()->translatedFormat('l, d F Y');
    }
}
