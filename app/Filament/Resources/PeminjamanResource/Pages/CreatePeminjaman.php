<?php
namespace App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_peminjaman'] = Peminjaman::generateKode();
        return $data;
    }
}

