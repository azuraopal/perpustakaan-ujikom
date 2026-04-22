<?php
namespace App\Filament\Resources\DendaResource\Pages;
use App\Filament\Resources\DendaResource;
use Filament\Resources\Pages\CreateRecord;
class CreateDenda extends CreateRecord
{
    protected static string $resource = DendaResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}

