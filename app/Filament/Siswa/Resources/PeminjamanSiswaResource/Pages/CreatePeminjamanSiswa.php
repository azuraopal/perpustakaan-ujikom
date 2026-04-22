<?php

namespace App\Filament\Siswa\Resources\PeminjamanSiswaResource\Pages;

use App\Filament\Siswa\Resources\PeminjamanSiswaResource;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePeminjamanSiswa extends CreateRecord
{
    protected static string $resource = PeminjamanSiswaResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_peminjaman'] = Peminjaman::generateKode();
        $data['user_id'] = Auth::id();
        $data['tanggal_pinjam'] = now()->toDateString();

        $durasi = (int) Pengaturan::getValue('durasi_pinjam', '7');
        $data['tanggal_harus_kembali'] = now()->addDays($durasi)->toDateString();
        $data['status'] = 'dipinjam';
        $data['jumlah'] = 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Kurangi stok buku
        $this->record->buku->decrement('stok', $this->record->jumlah);

        Notification::make()
            ->title('Peminjaman Berhasil!')
            ->body("Kode: {$this->record->kode_peminjaman}")
            ->success()
            ->send();
    }
}

