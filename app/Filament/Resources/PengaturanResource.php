<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanResource\Pages;
use App\Models\Pengaturan;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PengaturanResource extends Resource
{
    protected static ?string $model = Pengaturan::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('kunci')->required()->maxLength(100),
            TextInput::make('nilai')->required()->maxLength(255),
            Textarea::make('deskripsi')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kunci')->searchable()->sortable(),
                TextColumn::make('nilai')->searchable(),
                TextColumn::make('deskripsi')->limit(50),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturans::route('/'),
            'create' => Pages\CreatePengaturan::route('/create'),
            'edit' => Pages\EditPengaturan::route('/{record}/edit'),
        ];
    }
}

