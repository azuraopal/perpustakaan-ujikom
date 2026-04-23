<?php

namespace App\Filament\Siswa\Pages;

use App\Filament\Siswa\Resources\PeminjamanSiswaResource;
use App\Models\Buku;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class KatalogBuku extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Katalog Buku';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Katalog Buku';
    protected string $view = 'filament.siswa.pages.katalog-buku';

    public string $search = '';
    public string $kategoriFilter = '';

    public function getBooks(): Collection
    {
        $query = Buku::with(['kategori', 'rakBuku']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'ilike', "%{$this->search}%")
                  ->orWhere('penulis', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->kategoriFilter) {
            $query->where('kategori_id', $this->kategoriFilter);
        }

        return $query->orderBy('judul')->get();
    }

    public function getKategoris(): Collection
    {
        return \App\Models\Kategori::orderBy('nama')->get();
    }

    public function bukaFormPinjam(int $bukuId): void
    {
        $buku = Buku::findOrFail($bukuId);

        if ($buku->stok <= 0) {
            Notification::make()
                ->title('Stok Habis')
                ->body('Buku ini sedang tidak tersedia.')
                ->danger()
                ->send();
            return;
        }

        $this->redirect(PeminjamanSiswaResource::getUrl('create', ['buku' => $buku->id], panel: 'siswa'));
    }
}
