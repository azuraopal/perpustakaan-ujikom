<?php
namespace App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_peminjaman'] = Peminjaman::generateKode();
        return $data;
    }
}

