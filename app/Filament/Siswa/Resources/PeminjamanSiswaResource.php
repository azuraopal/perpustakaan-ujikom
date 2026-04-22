<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\PeminjamanSiswaResource\Pages;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PeminjamanSiswaResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Peminjaman Saya';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'peminjaman-saya';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('buku_id')
                ->label('Pilih Buku')
                ->relationship('buku', 'judul', fn ($query) => $query->where('stok', '>', 0))
                ->searchable()
                ->preload()
                ->required(),
            Textarea::make('catatan')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_peminjaman')->searchable(),
                TextColumn::make('buku.judul')->label('Buku')->limit(30),
                TextColumn::make('tanggal_pinjam')->date('d/m/Y'),
                TextColumn::make('tanggal_harus_kembali')->date('d/m/Y'),
                TextColumn::make('tanggal_dikembalikan')->date('d/m/Y'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam' => 'warning',
                        'dikembalikan' => 'success',
                        'terlambat' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn (Peminjaman $record) => in_array($record->status, ['dipinjam', 'terlambat']))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah Anda yakin ingin mengembalikan buku ini? Admin akan memverifikasi pengembalian.')
                    ->action(function (Peminjaman $record) {
                        $record->update([
                            'tanggal_dikembalikan' => now(),
                            'status' => 'dikembalikan',
                        ]);

                        $record->buku->increment('stok', $record->jumlah);

                        if (now()->greaterThan($record->tanggal_harus_kembali)) {
                            $hariTerlambat = (int) now()->diffInDays($record->tanggal_harus_kembali);
                            $dendaPerHari = (int) Pengaturan::getValue('denda_per_hari', '1000');

                            Denda::create([
                                'peminjaman_id' => $record->id,
                                'user_id' => $record->user_id,
                                'jenis_denda' => 'keterlambatan',
                                'jumlah_hari' => $hariTerlambat,
                                'nominal' => $hariTerlambat * $dendaPerHari,
                                'status_bayar' => 'belum_bayar',
                            ]);
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamanSiswas::route('/'),
            'create' => Pages\CreatePeminjamanSiswa::route('/create'),
        ];
    }
}

