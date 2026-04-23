<?php

namespace App\Filament\Resources\RakBukuResource\Pages;

use App\Filament\Resources\RakBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRakBukus extends ManageRecords
{
    protected static string $resource = RakBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
