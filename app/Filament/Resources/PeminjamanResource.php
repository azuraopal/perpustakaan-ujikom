<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|null
    {
        return 'Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Data Peminjaman')->schema([
                TextInput::make('kode_peminjaman')
                    ->default(fn () => Peminjaman::generateKode())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('user_id')
                    ->label('Peminjam')
                    ->relationship('user', 'nama_lengkap', fn ($query) => $query->where('role', 'siswa'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('buku_id')
                    ->label('Buku')
                    ->relationship('buku', 'judul', fn ($query) => $query->where('stok', '>', 0))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal_pinjam')
                    ->required()
                    ->default(now()),
                DatePicker::make('tanggal_harus_kembali')
                    ->required()
                    ->default(now()->addDays(7)),
                TextInput::make('jumlah')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Select::make('status')
                    ->options([
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                    ])
                    ->default('dipinjam')
                    ->required(),
                DatePicker::make('tanggal_dikembalikan')
                    ->label('Tanggal Dikembalikan'),
                Select::make('kondisi_buku')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                        'hilang' => 'Hilang',
                    ])
                    ->default('baik'),
                Textarea::make('catatan')->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_peminjaman')->searchable()->sortable(),
                TextColumn::make('user.nama_lengkap')->label('Peminjam')->searchable(),
                TextColumn::make('buku.judul')->label('Buku')->searchable()->limit(30),
                TextColumn::make('tanggal_pinjam')->date('d/m/Y')->sortable(),
                TextColumn::make('tanggal_harus_kembali')->date('d/m/Y')->sortable(),
                TextColumn::make('tanggal_dikembalikan')->date('d/m/Y'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam' => 'warning',
                        'dikembalikan' => 'success',
                        'terlambat' => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                    ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamans::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}
