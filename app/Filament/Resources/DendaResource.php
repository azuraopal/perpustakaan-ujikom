<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DendaResource\Pages;
use App\Models\Denda;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class DendaResource extends Resource
{
    protected static ?string $model = Denda::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Denda';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string|null
    {
        return 'Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Denda')
                ->description('Data pelanggaran dan nilai denda.')
                ->icon('heroicon-o-banknotes')
                ->columnSpanFull()
                ->schema([
                    Select::make('peminjaman_id')
                        ->label('Peminjaman')
                        ->relationship('peminjaman', 'kode_peminjaman')
                        ->native(false)
                        ->placeholder('Pilih kode peminjaman')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('user_id')
                        ->label('Siswa')
                        ->relationship('user', 'nama_lengkap', fn ($query) => $query->where('role', 'siswa'))
                        ->native(false)
                        ->placeholder('Pilih siswa')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('jenis_denda')
                        ->options([
                            'keterlambatan' => 'Keterlambatan',
                            'kerusakan' => 'Kerusakan',
                            'kehilangan' => 'Kehilangan',
                        ])
                        ->native(false)
                        ->required(),
                    TextInput::make('jumlah_hari')
                        ->numeric()
                        ->label('Jumlah Hari Terlambat')
                        ->placeholder('Contoh: 3'),
                    TextInput::make('nominal')
                        ->numeric()
                        ->required()
                        ->prefix('Rp')
                        ->placeholder('0')
                        ->minValue(0),
                    Select::make('status_bayar')
                        ->options([
                            'belum_bayar' => 'Belum Bayar',
                            'sudah_bayar' => 'Sudah Bayar',
                        ])
                        ->native(false)
                        ->default('belum_bayar')
                        ->required(),
                ])
                ->columns(2),

            Section::make('Pembayaran')
                ->description('Tanggal pelunasan denda.')
                ->icon('heroicon-o-receipt-percent')
                ->columnSpanFull()
                ->schema([
                    DatePicker::make('tanggal_bayar')
                        ->label('Tanggal Bayar')
                        ->helperText('Isi saat denda sudah dilunasi.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman.kode_peminjaman')->label('Kode Pinjam')->searchable(),
                TextColumn::make('user.nama_lengkap')->label('Siswa')->searchable(),
                TextColumn::make('jenis_denda')->badge(),
                TextColumn::make('nominal')->money('IDR')->sortable(),
                TextColumn::make('status_bayar')
                    ->badge()
                    ->color(fn (string $state) => $state === 'sudah_bayar' ? 'success' : 'danger'),
                TextColumn::make('tanggal_bayar')->date('d/m/Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_bayar')
                    ->options(['belum_bayar' => 'Belum Bayar', 'sudah_bayar' => 'Sudah Bayar']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDendas::route('/'),
            'create' => Pages\CreateDenda::route('/create'),
            'edit' => Pages\EditDenda::route('/{record}/edit'),
        ];
    }
}
