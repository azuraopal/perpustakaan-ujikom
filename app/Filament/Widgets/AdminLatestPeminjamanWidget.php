<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminLatestPeminjamanWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $heading = 'Peminjaman Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('kode_peminjaman')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Siswa'),
                Tables\Columns\TextColumn::make('buku.judul')
                    ->label('Buku')
                    ->limit(30)
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
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Peminjaman::statusLabel($state))
                    ->color(fn (string $state): string => Peminjaman::statusColor($state)),
            ])
            ->paginated(false);
    }
}
