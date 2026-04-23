<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DendaResource\Pages;
use App\Models\Denda;
use App\Models\LogAktivitas;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
    protected static ?string $modelLabel = 'Denda';
    protected static ?string $pluralModelLabel = 'Denda';
    protected static ?int $navigationSort = 6;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Transaksi';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Denda::where('status_bayar', Denda::STATUS_BELUM_BAYAR)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
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
                            Denda::STATUS_BELUM_BAYAR => 'Belum Lunas',
                            Denda::STATUS_SUDAH_BAYAR => 'Sudah Lunas',
                        ])
                        ->native(false)
                        ->default(Denda::STATUS_BELUM_BAYAR)
                        ->required(),
                ])
                ->columns(2),

            Section::make('Pembayaran')
                ->description('Metode dan tanggal pelunasan.')
                ->icon('heroicon-o-receipt-percent')
                ->aside()
                ->schema([
                    Select::make('metode_pembayaran')
                        ->options([
                            'cash' => 'Tunai',
                            'midtrans' => 'Online (Midtrans)',
                        ])
                        ->native(false)
                        ->placeholder('Belum ada pembayaran'),
                    DatePicker::make('tanggal_bayar')
                        ->label('Tanggal Bayar')
                        ->helperText('Isi saat denda sudah dilunasi.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman.kode_peminjaman')->label('Kode Pinjam')->searchable(),
                TextColumn::make('user.nama_lengkap')->label('Siswa')->searchable(),
                TextColumn::make('jenis_denda')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Denda::jenisDendaLabel($state))
                    ->color('gray'),
                TextColumn::make('nominal')->money('IDR')->sortable(),
                TextColumn::make('status_bayar')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Denda::statusBayarLabel($state))
                    ->color(fn (string $state) => Denda::statusBayarColor($state)),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->formatStateUsing(fn (?string $state) => Denda::metodePembayaranLabel($state))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('tanggal_bayar')->date('d/m/Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_bayar')
                    ->options([
                        Denda::STATUS_BELUM_BAYAR => 'Belum Lunas',
                        Denda::STATUS_SUDAH_BAYAR => 'Sudah Lunas',
                    ]),
            ])
            ->actions([
                Action::make('konfirmasiCash')
                    ->label('Konfirmasi Cash')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Denda $record): bool => $record->status_bayar === Denda::STATUS_BELUM_BAYAR)
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalHeading('Konfirmasi Pembayaran Tunai')
                    ->modalDescription('Apakah siswa sudah membayar denda ini secara tunai?')
                    ->action(function (Denda $record): void {
                        $record->update([
                            'status_bayar' => Denda::STATUS_SUDAH_BAYAR,
                            'metode_pembayaran' => 'cash',
                            'tanggal_bayar' => now()->toDateString(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran Tunai Dikonfirmasi')
                            ->body('Denda telah ditandai sebagai lunas.')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Denda Lunas')
                            ->body("Pembayaran denda tunai untuk kode {$record->peminjaman->kode_peminjaman} telah dikonfirmasi oleh admin.")
                            ->success()
                            ->sendToDatabase($record->user);

                        LogAktivitas::create([
                            'user_id' => auth()->id(),
                            'aktivitas' => 'Konfirmasi Pembayaran Cash',
                            'detail' => "Mengkonfirmasi pembayaran tunai denda Rp " . number_format($record->nominal, 0, ',', '.') . " untuk {$record->user->nama_lengkap}.",
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
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
