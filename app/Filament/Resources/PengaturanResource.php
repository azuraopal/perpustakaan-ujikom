<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanResource\Pages;
use App\Models\Pengaturan;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\EditAction;
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
            TextInput::make('kunci')
                ->required()
                ->placeholder('Contoh: denda_per_hari')
                ->prefixIcon('heroicon-o-key')
                ->helperText('Gunakan format snake_case.')
                ->maxLength(100),
            TextInput::make('nilai')
                ->required()
                ->placeholder('Contoh: 1000')
                ->prefixIcon('heroicon-o-adjustments-horizontal')
                ->maxLength(255),
            Textarea::make('deskripsi')
                ->rows(3)
                ->placeholder('Jelaskan fungsi pengaturan ini...')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kunci')->searchable()->sortable()
                    ->badge()->color('gray'),
                TextColumn::make('nilai')->searchable()
                    ->copyable(),
                TextColumn::make('deskripsi')->limit(50),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePengaturans::route('/'),
        ];
    }
}
