<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuResource\Pages;
use App\Models\Buku;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BukuResource extends Resource
{
    protected static ?string $model = Buku::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Data Buku';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|null
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Buku')->schema([
                TextInput::make('judul')->required()->maxLength(255),
                TextInput::make('isbn')->maxLength(20),
                TextInput::make('penulis')->required()->maxLength(150),
                TextInput::make('penerbit')->required()->maxLength(150),
                TextInput::make('tahun_terbit')->numeric()->required()->minValue(1900)->maxValue(date('Y')),
                TextInput::make('stok')->numeric()->required()->default(0)->minValue(0),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')->required(),
                    ]),
                Select::make('rak_buku_id')
                    ->label('Rak Buku')
                    ->relationship('rakBuku', 'nama')
                    ->searchable()
                    ->preload(),
            ])->columns(2),

            Section::make('Detail Tambahan')->schema([
                Textarea::make('sinopsis')->rows(4),
                FileUpload::make('cover_image')
                    ->label('Cover Buku')
                    ->image()
                    ->directory('cover-buku')
                    ->maxSize(2048),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('Cover')->square()->size(50),
                TextColumn::make('judul')->searchable()->sortable()->limit(40),
                TextColumn::make('penulis')->searchable()->sortable(),
                TextColumn::make('kategori.nama')->label('Kategori')->sortable(),
                TextColumn::make('tahun_terbit')->sortable(),
                TextColumn::make('stok')->sortable()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('rakBuku.nama')->label('Rak'),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBukus::route('/'),
            'create' => Pages\CreateBuku::route('/create'),
            'edit' => Pages\EditBuku::route('/{record}/edit'),
        ];
    }
}

