<?php
namespace App\Filament\Resources\RakBukuResource\Pages;
use App\Filament\Resources\RakBukuResource;
use Filament\Resources\Pages\CreateRecord;
class CreateRakBuku extends CreateRecord
{
    protected static string $resource = RakBukuResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}

