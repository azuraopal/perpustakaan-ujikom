<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\PeminjamanSiswaResource\Pages;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PeminjamanSiswaResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Peminjaman Saya';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'peminjaman-saya';

    public static function getNavigationBadge(): ?string
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        if (! $userId) {
            return null;
        }
        $count = Peminjaman::where('user_id', $userId)
            ->whereIn('status', Peminjaman::activeLoanStatuses())
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        $maxPinjam = (int) Pengaturan::getValue('max_peminjaman', '3');
        $aktifPinjam = Auth::check()
            ? (int) Peminjaman::query()
                ->where('user_id', Auth::id())
                ->whereIn('status', Peminjaman::activeLoanStatuses())
                ->sum('jumlah')
            : 0;
        $sisaKuota = max(0, $maxPinjam - $aktifPinjam);

        return $schema->schema([
            Section::make('Daftar Buku yang Dipinjam')
                ->description("Tambahkan buku yang ingin dipinjam. Kuota maksimal {$maxPinjam} buku, saat ini aktif {$aktifPinjam} buku, sisa kuota {$sisaKuota} buku.")
                ->icon('heroicon-o-book-open')
                ->schema([
                    Repeater::make('items')
                        ->label('')
                        ->default(function (): array {
                            $bookId = request()->integer('buku');

                            return [[
                                'buku_id' => $bookId ?: null,
                                'jumlah' => 1,
                            ]];
                        })
                        ->minItems(1)
                        ->maxItems($maxPinjam)
                        ->disabled($sisaKuota <= 0)
                        ->addActionLabel('+ Tambah Buku Lain')
                        ->reorderable(false)
                        ->itemLabel(fn (array $state): string => isset($state['buku_id']) && $state['buku_id']
                            ? 'Buku #' . (Buku::find($state['buku_id'])?->judul ?? $state['buku_id'])
                            : 'Buku Baru')
                        ->schema([
                            Select::make('buku_id')
                                ->label('Pilih Buku')
                                ->options(fn () => Buku::where('stok', '>', 0)->pluck('judul', 'id'))
                                ->native(false)
                                ->placeholder('Ketik judul buku untuk mencari...')
                                ->prefixIcon('heroicon-o-book-open')
                                ->helperText('Hanya buku dengan stok tersedia yang ditampilkan.')
                                ->searchable()
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('jumlah')
                                ->label('Jumlah Eksemplar')
                                ->helperText('Masukkan jumlah eksemplar yang ingin dipinjam untuk buku ini.')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(5)
                                ->prefixIcon('heroicon-o-hashtag')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make('Jadwal Pengembalian')
                ->description('Tentukan kapan Anda berencana mengembalikan semua buku dalam pengajuan ini.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    DatePicker::make('tanggal_harus_kembali')
                        ->label('Tanggal Rencana Kembali')
                        ->helperText('Tanggal ini berlaku untuk semua buku di atas. Admin akan meninjau tanggal ini saat menyetujui pengajuan Anda.')
                        ->native(false)
                        ->prefixIcon('heroicon-o-calendar')
                        ->minDate(now()->addDay())
                        ->default(fn (): string => now()->addDays((int) Pengaturan::getValue('durasi_pinjam', '7'))->toDateString())
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make('Catatan Tambahan')
                ->description('Opsional. Tulis catatan jika ada informasi tambahan untuk admin.')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->schema([
                    Textarea::make('catatan')
                        ->label('Catatan Pengajuan')
                        ->rows(6)
                        ->placeholder("Contoh:\n- Diperlukan untuk tugas Bahasa Indonesia minggu depan\n- Buku akan dipakai untuk presentasi kelompok\n- Mohon diprioritaskan karena deadline tugas")
                        ->helperText('Berikan alasan peminjaman agar admin lebih mudah menyetujui pengajuan Anda.')
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible(),
        ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_peminjaman')->searchable(),
                TextColumn::make('buku.judul')->label('Buku')->limit(30),
                TextColumn::make('jumlah')->label('Jumlah')->alignCenter(),
                TextColumn::make('tanggal_pinjam')->label('Tanggal Pengajuan')->date('d/m/Y'),
                TextColumn::make('tanggal_harus_kembali')->label('Rencana Kembali')->date('d/m/Y'),
                TextColumn::make('tanggal_dikembalikan')->date('d/m/Y'),
                TextColumn::make('display_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Peminjaman::statusLabel($state))
                    ->color(fn (string $state): string => Peminjaman::statusColor($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('ajukanPengembalian')
                    ->label('Ajukan Pengembalian')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn (Peminjaman $record): bool => in_array($record->display_status, [Peminjaman::STATUS_DIPINJAM, Peminjaman::STATUS_TERLAMBAT], true))
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Verifikasi Pengembalian')
                    ->modalDescription('Isi kondisi buku. Setelah diajukan, admin akan memverifikasi pengembalian Anda.')
                    ->form([
                        Select::make('kondisi_buku')
                            ->label('Kondisi Buku Saat Dikembalikan')
                            ->options([
                                'baik' => 'Baik',
                                'rusak_ringan' => 'Rusak Ringan',
                                'rusak_berat' => 'Rusak Berat',
                                'hilang' => 'Hilang',
                            ])
                            ->native(false)
                            ->default('baik')
                            ->required(),
                        Textarea::make('catatan_pengembalian')
                            ->label('Catatan Pengembalian')
                            ->rows(3)
                            ->placeholder('Opsional: jelaskan kondisi buku jika ada kerusakan.'),
                    ])
                    ->action(function (Peminjaman $record, array $data): void {
                        $record->update([
                            'tanggal_dikembalikan' => now(),
                            'status' => Peminjaman::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN,
                            'pengembalian_diajukan_pada' => now(),
                            'kondisi_buku' => $data['kondisi_buku'],
                            'catatan_pengembalian' => $data['catatan_pengembalian'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Pengembalian Berhasil Diajukan')
                            ->body('Admin akan memverifikasi pengembalian dan menghitung denda jika diperlukan.')
                            ->success()
                            ->send();

                        // Bell notification to admins
                        $admins = \App\Models\User::where('role', 'admin')->get();
                        if ($admins->isNotEmpty()) {
                            Notification::make()
                                ->title('Pengajuan Pengembalian Buku')
                                ->body(Auth::user()->nama_lengkap . " mengajukan pengembalian buku '{$record->buku->judul}'.")
                                ->info()
                                ->sendToDatabase($admins);
                        }

                        // Bell notification for siswa (persisted)
                        Notification::make()
                            ->title('Pengembalian Diajukan')
                            ->body("Pengajuan pengembalian buku '{$record->buku->judul}' sedang menunggu verifikasi admin.")
                            ->info()
                            ->sendToDatabase(Auth::user());

                        \App\Models\LogAktivitas::create([
                            'user_id' => Auth::id(),
                            'aktivitas' => 'Pengajuan Pengembalian',
                            'detail' => "Siswa mengajukan pengembalian buku '{$record->buku->judul}' (kode: {$record->kode_peminjaman}).",
                        ]);
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
