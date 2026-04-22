<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DendaResource\Pages;
use App\Models\Denda;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
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
            Select::make('peminjaman_id')
                ->label('Peminjaman')
                ->relationship('peminjaman', 'kode_peminjaman')
                ->searchable()->preload()->required(),
            Select::make('user_id')
                ->label('Siswa')
                ->relationship('user', 'nama_lengkap', fn ($query) => $query->where('role', 'siswa'))
                ->searchable()->preload()->required(),
            Select::make('jenis_denda')
                ->options(['keterlambatan' => 'Keterlambatan', 'kerusakan' => 'Kerusakan', 'kehilangan' => 'Kehilangan'])
                ->required(),
            TextInput::make('jumlah_hari')->numeric()->label('Jumlah Hari Terlambat'),
            TextInput::make('nominal')->numeric()->required()->prefix('Rp'),
            Select::make('status_bayar')
                ->options(['belum_bayar' => 'Belum Bayar', 'sudah_bayar' => 'Sudah Bayar'])
                ->default('belum_bayar')->required(),
            DatePicker::make('tanggal_bayar'),
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

