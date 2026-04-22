<?php

namespace App\Filament\Siswa\Pages;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
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

    public function pinjamBuku(int $bukuId): void
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

        $maxPinjam = (int) Pengaturan::getValue('max_peminjaman', '3');
        $currentPinjam = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->count();

        if ($currentPinjam >= $maxPinjam) {
            Notification::make()
                ->title('Batas Peminjaman Tercapai')
                ->body("Anda sudah meminjam {$currentPinjam} buku. Maksimal {$maxPinjam} buku.")
                ->danger()
                ->send();
            return;
        }

        $durasi = (int) Pengaturan::getValue('durasi_pinjam', '7');

        Peminjaman::create([
            'kode_peminjaman' => Peminjaman::generateKode(),
            'user_id' => auth()->id(),
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_harus_kembali' => now()->addDays($durasi)->toDateString(),
            'jumlah' => 1,
            'status' => 'dipinjam',
        ]);

        $buku->decrement('stok');

        Notification::make()
            ->title('Peminjaman Berhasil!')
            ->body("Buku \"{$buku->judul}\" berhasil dipinjam. Kembalikan sebelum " . now()->addDays($durasi)->format('d/m/Y'))
            ->success()
            ->send();
    }
}
