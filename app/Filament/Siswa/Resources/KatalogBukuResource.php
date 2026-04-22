<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\KatalogBukuResource\Pages;
use App\Models\Buku;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KatalogBukuResource extends Resource
{
    protected static ?string $model = Buku::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Katalog Buku';
    protected static ?string $modelLabel = 'Buku';
    protected static ?string $pluralModelLabel = 'Katalog Buku';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'katalog-buku';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('Cover')->square()->size(50),
                TextColumn::make('judul')->searchable()->sortable(),
                TextColumn::make('penulis')->searchable(),
                TextColumn::make('kategori.nama')->label('Kategori'),
                TextColumn::make('penerbit'),
                TextColumn::make('tahun_terbit'),
                TextColumn::make('stok')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('rakBuku.nama')->label('Rak'),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKatalogBukus::route('/'),
        ];
    }
}
