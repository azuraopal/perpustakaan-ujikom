<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Models\Kategori;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori';
    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategori';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|null
    {
        return 'Master Data';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Kategori::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('nama')
                ->required()
                ->placeholder('Contoh: Novel')
                ->prefixIcon('heroicon-o-tag')
                ->maxLength(100)
                ->columnSpanFull(),
            Textarea::make('deskripsi')
                ->rows(3)
                ->placeholder('Deskripsi singkat kategori')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('deskripsi')->limit(50),
                TextColumn::make('bukus_count')->counts('bukus')->label('Jumlah Buku')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKategoris::route('/'),
        ];
    }
}
