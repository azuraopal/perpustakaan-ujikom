<?php

namespace App\Filament\Widgets;

use App\Models\LogAktivitas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminLogAktivitasWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $heading = 'Log Aktivitas Sistem';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LogAktivitas::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('User')
                    ->default('Sistem'),
                Tables\Columns\TextColumn::make('aktivitas')
                    ->label('Aktivitas')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('detail')
                    ->label('Detail')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->paginated(false);
    }
}
