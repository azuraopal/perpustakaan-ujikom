<?php

namespace App\Filament\Siswa\Resources\PeminjamanSiswaResource\Pages;

use App\Filament\Siswa\Resources\PeminjamanSiswaResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreatePeminjamanSiswa extends CreateRecord
{
    protected static string $resource = PeminjamanSiswaResource::class;
    protected static bool $canCreateAnother = false;
    protected array $loanItems = [];

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index', panel: 'siswa');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = collect($data['items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['buku_id'] ?? null))
            ->map(function (array $item): array {
                return [
                    'buku_id' => (int) $item['buku_id'],
                    'jumlah' => max(1, (int) ($item['jumlah'] ?? 1)),
                ];
            })
            ->groupBy('buku_id')
            ->map(fn ($group, $bookId): array => [
                'buku_id' => (int) $bookId,
                'jumlah' => (int) $group->sum('jumlah'),
            ])
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Tambahkan minimal 1 buku untuk diajukan.',
            ]);
        }

        $maxPinjam = (int) Pengaturan::getValue('max_peminjaman', '3');
        $aktifPinjam = Peminjaman::query()
            ->where('user_id', Auth::id())
            ->whereIn('status', Peminjaman::activeLoanStatuses())
            ->sum('jumlah');

        $jumlahDiajukan = (int) $items->sum('jumlah');

        if (($aktifPinjam + $jumlahDiajukan) > $maxPinjam) {
            throw ValidationException::withMessages([
                'items' => "Batas peminjaman terlampaui. Anda mengajukan {$jumlahDiajukan} buku, sementara kuota tersisa " . max(0, $maxPinjam - $aktifPinjam) . ' buku.',
            ]);
        }

        $errors = [];
        foreach ($items as $index => $item) {
            $buku = Buku::find($item['buku_id']);

            if (! $buku) {
                $errors["items.{$index}.buku_id"] = 'Buku tidak ditemukan.';
                continue;
            }

            if ($buku->stok < $item['jumlah']) {
                $errors["items.{$index}.jumlah"] = "Stok '{$buku->judul}' hanya tersedia {$buku->stok} eksemplar.";
            }

            $sedangDipinjam = Peminjaman::query()
                ->where('user_id', Auth::id())
                ->where('buku_id', $item['buku_id'])
                ->whereIn('status', Peminjaman::activeLoanStatuses())
                ->exists();

            if ($sedangDipinjam) {
                $errors["items.{$index}.buku_id"] = "Anda masih meminjam buku '{$buku->judul}'. Kembalikan terlebih dahulu sebelum meminjam lagi.";
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $this->loanItems = $items->all();
        $firstItem = $this->loanItems[0];

        $data['kode_peminjaman'] = Peminjaman::generateKode();
        $data['user_id'] = Auth::id();
        $data['tanggal_pinjam'] = now()->toDateString();
        $data['buku_id'] = $firstItem['buku_id'];
        $data['jumlah'] = $firstItem['jumlah'];

        $data['status'] = Peminjaman::STATUS_MENUNGGU_PERSETUJUAN;
        $data['kondisi_buku'] = 'baik';

        unset($data['items']);

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach (array_slice($this->loanItems, 1) as $item) {
            Peminjaman::create([
                'kode_peminjaman' => Peminjaman::generateKode(),
                'user_id' => Auth::id(),
                'buku_id' => $item['buku_id'],
                'tanggal_pinjam' => $this->record->tanggal_pinjam,
                'tanggal_harus_kembali' => $this->record->tanggal_harus_kembali,
                'jumlah' => $item['jumlah'],
                'status' => Peminjaman::STATUS_MENUNGGU_PERSETUJUAN,
                'kondisi_buku' => 'baik',
                'catatan' => $this->record->catatan,
            ]);
        }

        $jumlahItem = count($this->loanItems);

        Notification::make()
            ->title('Pengajuan Berhasil Dikirim')
            ->body("{$jumlahItem} buku berhasil diajukan dan menunggu persetujuan admin.")
            ->success()
            ->send();

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('Pengajuan Peminjaman Baru')
                ->body(Auth::user()->nama_lengkap . " mengajukan peminjaman {$jumlahItem} buku.")
                ->info()
                ->sendToDatabase($admins);
        }

        \App\Models\LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Pengajuan Peminjaman',
            'detail' => "Siswa mengajukan {$jumlahItem} buku untuk dipinjam.",
        ]);
    }
}

