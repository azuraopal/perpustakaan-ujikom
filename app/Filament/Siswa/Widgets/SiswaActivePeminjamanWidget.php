<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class SiswaActivePeminjamanWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Pinjaman Saya Saat Ini';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('status', Peminjaman::activeLoanStatuses())
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('buku.judul')
                    ->label('Buku')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('tanggal_harus_kembali')
                    ->label('Batas Kembali')
                    ->date('d M Y')
                    ->color(fn ($record) => $record->tanggal_harus_kembali?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Peminjaman::statusLabel($state))
                    ->color(fn (string $state): string => Peminjaman::statusColor($state)),
            ])
            ->paginated(false);
    }
}
