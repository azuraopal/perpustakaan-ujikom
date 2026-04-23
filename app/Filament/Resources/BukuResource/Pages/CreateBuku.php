<?php
namespace App\Filament\Resources\BukuResource\Pages;
use App\Filament\Resources\BukuResource;
use App\Models\LogAktivitas;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBuku extends CreateRecord
{
    protected static string $resource = BukuResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $buku = $this->record;

        // Notify all siswa
        $siswa = User::where('role', 'siswa')->get();
        if ($siswa->isNotEmpty()) {
            Notification::make()
                ->title('Buku Baru Tersedia!')
                ->body("Buku baru \"{$buku->judul}\" telah ditambahkan ke perpustakaan. Cek katalog sekarang!")
                ->info()
                ->sendToDatabase($siswa);
        }

        // Log activity
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Tambah Buku',
            'detail' => "Menambahkan buku baru: {$buku->judul}.",
        ]);
    }
}
