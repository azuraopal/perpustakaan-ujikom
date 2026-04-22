<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RakBukuResource\Pages;
use App\Models\RakBuku;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RakBukuResource extends Resource
{
    protected static ?string $model = RakBuku::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Rak Buku';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|null
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Identitas Rak')
                ->description('Atur informasi rak agar pengelolaan lokasi buku lebih terstruktur.')
                ->icon('heroicon-o-archive-box')
                ->compact()
                ->schema([
                    TextInput::make('kode_rak')
                        ->required()
                        ->placeholder('Contoh: R-01')
                        ->prefixIcon('heroicon-o-hashtag')
                        ->maxLength(20),
                    TextInput::make('nama')
                        ->required()
                        ->placeholder('Nama rak')
                        ->prefixIcon('heroicon-o-archive-box')
                        ->maxLength(100),
                    TextInput::make('lokasi')
                        ->placeholder('Contoh: Lantai 2 - Zona Timur')
                        ->prefixIcon('heroicon-o-map-pin')
                        ->maxLength(100),
                    TextInput::make('kapasitas')
                        ->numeric()
                        ->placeholder('Contoh: 120')
                        ->prefixIcon('heroicon-o-squares-plus'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_rak')->searchable()->sortable(),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('lokasi'),
                TextColumn::make('kapasitas'),
                TextColumn::make('bukus_count')->counts('bukus')->label('Jumlah Buku'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRakBukus::route('/'),
            'create' => Pages\CreateRakBuku::route('/create'),
            'edit' => Pages\EditRakBuku::route('/{record}/edit'),
        ];
    }
}
