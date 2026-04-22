<?php
namespace App\Filament\Resources\RakBukuResource\Pages;
use App\Filament\Resources\RakBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditRakBuku extends EditRecord
{
    protected static string $resource = RakBukuResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}

