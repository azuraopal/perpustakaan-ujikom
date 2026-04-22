<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\DendaSiswaResource\Pages;
use App\Models\Denda;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DendaSiswaResource extends Resource
{
    protected static ?string $model = Denda::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Denda Saya';
    protected static ?string $modelLabel = 'Denda';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'denda-saya';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman.kode_peminjaman')->label('Kode Pinjam'),
                TextColumn::make('jenis_denda')->badge(),
                TextColumn::make('jumlah_hari')->label('Hari Terlambat'),
                TextColumn::make('nominal')->money('IDR'),
                TextColumn::make('status_bayar')
                    ->badge()
                    ->color(fn (string $state) => $state === 'sudah_bayar' ? 'success' : 'danger'),
                TextColumn::make('tanggal_bayar')->date('d/m/Y'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDendaSiswas::route('/'),
        ];
    }
}
