<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 5;

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
        $count = Peminjaman::where('status', Peminjaman::STATUS_MENUNGGU_PERSETUJUAN)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Data Peminjaman')
                ->description('Informasi pengajuan peminjaman dari siswa.')
                ->icon('heroicon-o-arrow-right-circle')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('kode_peminjaman')
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('user_id')
                        ->label('Peminjam')
                        ->relationship('user', 'nama_lengkap', fn ($query) => $query->where('role', 'siswa'))
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false)
                        ->searchable()
                        ->preload(),
                    Select::make('buku_id')
                        ->label('Buku')
                        ->relationship('buku', 'judul')
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false)
                        ->searchable()
                        ->preload(),
                    TextInput::make('jumlah')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false)
                        ->minValue(1)
                        ->default(1),
                    DatePicker::make('tanggal_pinjam')
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false),
                    DatePicker::make('tanggal_harus_kembali')
                        ->required()
                        ->native(false),
                    Select::make('status')
                        ->options(Peminjaman::statusOptions())
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false),
                    Textarea::make('catatan')
                        ->label('Catatan Pengajuan')
                        ->rows(3)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Pengembalian & Persetujuan')
                ->description('Informasi proses persetujuan pengajuan dan verifikasi pengembalian.')
                ->icon('heroicon-o-arrow-uturn-left')
                ->columnSpanFull()
                ->schema([
                    DatePicker::make('tanggal_dikembalikan')
                        ->native(false)
                        ->label('Tanggal Dikembalikan'),
                    DatePicker::make('pengembalian_diajukan_pada')
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('kondisi_buku')
                        ->options([
                            'baik' => 'Baik',
                            'rusak_ringan' => 'Rusak Ringan',
                            'rusak_berat' => 'Rusak Berat',
                            'hilang' => 'Hilang',
                        ])
                        ->native(false)
                        ->default('baik'),
                    Textarea::make('catatan_pengembalian')
                        ->rows(3)
                        ->placeholder('Catatan dari siswa saat mengajukan pengembalian...')
                        ->columnSpanFull(),
                    Textarea::make('alasan_penolakan')
                        ->rows(3)
                        ->placeholder('Alasan saat pengajuan ditolak...')
                        ->columnSpanFull()
                        ->disabled()
                        ->dehydrated(false),
                    Textarea::make('catatan')
                        ->label('Catatan Admin')
                        ->rows(3)
                        ->placeholder('Catatan tambahan dari admin...')
                        ->columnSpanFull(),
                ])
                ->columns(2),
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
                TextColumn::make('display_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Peminjaman::statusLabel($state))
                    ->color(fn (string $state): string => Peminjaman::statusColor($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(Peminjaman::statusOptions()),
            ])
            ->actions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Peminjaman $record): bool => $record->status === Peminjaman::STATUS_MENUNGGU_PERSETUJUAN)
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan Peminjaman')
                    ->modalDescription('Saat disetujui, stok buku akan otomatis berkurang.')
                    ->action(function (Peminjaman $record): void {
                        if ($record->buku->stok < $record->jumlah) {
                            Notification::make()
                                ->title('Stok Tidak Cukup')
                                ->body('Pengajuan tidak bisa disetujui karena stok buku tidak mencukupi.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'status' => Peminjaman::STATUS_DIPINJAM,
                            'disetujui_oleh' => Auth::id(),
                            'disetujui_pada' => now(),
                            'ditolak_oleh' => null,
                            'ditolak_pada' => null,
                            'alasan_penolakan' => null,
                            'tanggal_pinjam' => now()->toDateString(),
                        ]);

                        $record->buku->decrement('stok', $record->jumlah);

                        Notification::make()
                            ->title('Pengajuan Disetujui')
                            ->body('Status peminjaman berubah menjadi dipinjam.')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Pengajuan Peminjaman Disetujui')
                            ->body("Pengajuan pinjam buku '{$record->buku->judul}' telah disetujui. Silakan ambil buku di perpustakaan.")
                            ->success()
                            ->sendToDatabase($record->user);

                        \App\Models\LogAktivitas::create([
                            'user_id' => Auth::id(),
                            'aktivitas' => 'Persetujuan Peminjaman',
                            'detail' => "Menyetujui pinjaman {$record->kode_peminjaman} (Buku: {$record->buku->judul}).",
                        ]);
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Peminjaman $record): bool => $record->status === Peminjaman::STATUS_MENUNGGU_PERSETUJUAN)
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->rows(3)
                            ->required(),
                    ])
                    ->action(function (Peminjaman $record, array $data): void {
                        $record->update([
                            'status' => Peminjaman::STATUS_DITOLAK,
                            'ditolak_oleh' => Auth::id(),
                            'ditolak_pada' => now(),
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan Ditolak')
                            ->body('Alasan penolakan telah disimpan dan terlihat di detail peminjaman.')
                            ->warning()
                            ->send();

                        Notification::make()
                            ->title('Pengajuan Peminjaman Ditolak')
                            ->body("Pengajuan pinjam buku '{$record->buku->judul}' ditolak. Alasan: {$data['alasan_penolakan']}")
                            ->danger()
                            ->sendToDatabase($record->user);

                        \App\Models\LogAktivitas::create([
                            'user_id' => Auth::id(),
                            'aktivitas' => 'Penolakan Peminjaman',
                            'detail' => "Menolak pinjaman {$record->kode_peminjaman} dengan alasan: {$data['alasan_penolakan']}.",
                        ]);
                    }),
                Action::make('verifikasiPengembalian')
                    ->label('Verifikasi Kembali')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->visible(fn (Peminjaman $record): bool => $record->status === Peminjaman::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN)
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pengembalian Buku')
                    ->modalDescription('Status akan diubah ke dikembalikan, stok ditambah, dan denda dibuat otomatis jika ada.')
                    ->action(function (Peminjaman $record): void {
                        $tanggalDikembalikan = $record->tanggal_dikembalikan ?? now()->toDateString();

                        $record->update([
                            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
                            'tanggal_dikembalikan' => $tanggalDikembalikan,
                            'pengembalian_diverifikasi_oleh' => Auth::id(),
                            'pengembalian_diverifikasi_pada' => now(),
                        ]);

                        $record->buku->increment('stok', $record->jumlah);

                        $hariTerlambat = 0;
                        if ($record->tanggal_dikembalikan && $record->tanggal_dikembalikan->greaterThan($record->tanggal_harus_kembali)) {
                            $hariTerlambat = (int) $record->tanggal_harus_kembali->diffInDays($record->tanggal_dikembalikan);
                        }

                        if ($hariTerlambat > 0) {
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

                        $dendaKondisi = match ($record->kondisi_buku) {
                            'rusak_ringan' => (int) Pengaturan::getValue('denda_rusak_ringan', '5000'),
                            'rusak_berat' => (int) Pengaturan::getValue('denda_rusak_berat', '15000'),
                            'hilang' => (int) Pengaturan::getValue('denda_hilang', '50000'),
                            default => 0,
                        };

                        if ($dendaKondisi > 0) {
                            Denda::create([
                                'peminjaman_id' => $record->id,
                                'user_id' => $record->user_id,
                                'jenis_denda' => $record->kondisi_buku === 'hilang' ? 'kehilangan' : 'kerusakan',
                                'jumlah_hari' => null,
                                'nominal' => $dendaKondisi,
                                'status_bayar' => 'belum_bayar',
                            ]);
                        }

                        Notification::make()
                            ->title('Pengembalian Diverifikasi')
                            ->body('Stok sudah diperbarui dan denda otomatis dibuat jika ada pelanggaran.')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Pengembalian Selesai')
                            ->body("Buku '{$record->buku->judul}' berhasil dikembalikan dan diverifikasi. Terima kasih!")
                            ->success()
                            ->sendToDatabase($record->user);

                        \App\Models\LogAktivitas::create([
                            'user_id' => Auth::id(),
                            'aktivitas' => 'Verifikasi Pengembalian',
                            'detail' => "Memverifikasi pengembalian {$record->kode_peminjaman} (Buku: {$record->buku->judul}).",
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
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
