<?php

namespace App\Filament\Resources\RakBukuResource\Pages;

use App\Filament\Resources\RakBukuResource;
use App\Models\LogAktivitas;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageRakBukus extends ManageRecords
{
    protected static string $resource = RakBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function ($record): void {
                    // Notify all siswa
                    $siswa = User::where('role', 'siswa')->get();
                    if ($siswa->isNotEmpty()) {
                        Notification::make()
                            ->title('Rak Buku Baru')
                            ->body("Rak baru \"{$record->nama_rak}\" telah ditambahkan di perpustakaan.")
                            ->info()
                            ->sendToDatabase($siswa);
                    }

                    // Log activity
                    LogAktivitas::create([
                        'user_id' => Auth::id(),
                        'aktivitas' => 'Tambah Rak Buku',
                        'detail' => "Menambahkan rak buku baru: {$record->nama_rak}.",
                    ]);
                }),
        ];
    }
}
