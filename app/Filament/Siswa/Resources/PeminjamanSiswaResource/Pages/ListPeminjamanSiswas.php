<?php
namespace App\Filament\Siswa\Resources\PeminjamanSiswaResource\Pages;
use App\Filament\Siswa\Resources\PeminjamanSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListPeminjamanSiswas extends ListRecords
{
    protected static string $resource = PeminjamanSiswaResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label('Pinjam Buku')]; }
}

