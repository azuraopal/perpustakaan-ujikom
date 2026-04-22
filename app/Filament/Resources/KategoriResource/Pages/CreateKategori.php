<?php
namespace App\Filament\Resources\KategoriResource\Pages;
use App\Filament\Resources\KategoriResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}

