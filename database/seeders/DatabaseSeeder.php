<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\RakBuku;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === ADMIN ===
        User::create([
            'nomor_induk' => 'ADM001',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@perpustakaan.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // === SISWA SAMPLE ===
        User::create([
            'nomor_induk' => '12345',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@siswa.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'kelas' => 'XII RPL 1',
            'is_active' => true,
        ]);

        User::create([
            'nomor_induk' => '12346',
            'nama_lengkap' => 'Siti Aminah',
            'email' => 'siti@siswa.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'kelas' => 'XII RPL 2',
            'is_active' => true,
        ]);

        // === KATEGORI ===
        $kategoris = ['Novel', 'Buku Pelajaran', 'Ensiklopedia', 'Komik', 'Biografi', 'Sains & Teknologi'];
        foreach ($kategoris as $nama) {
            Kategori::create(['nama' => $nama]);
        }

        // === RAK BUKU ===
        RakBuku::create(['kode_rak' => 'R-001', 'nama' => 'Rak A', 'lokasi' => 'Lantai 1', 'kapasitas' => 100]);
        RakBuku::create(['kode_rak' => 'R-002', 'nama' => 'Rak B', 'lokasi' => 'Lantai 1', 'kapasitas' => 100]);
        RakBuku::create(['kode_rak' => 'R-003', 'nama' => 'Rak C', 'lokasi' => 'Lantai 2', 'kapasitas' => 80]);

        // === BUKU SAMPLE ===
        $novel = Kategori::where('nama', 'Novel')->first();
        $pelajaran = Kategori::where('nama', 'Buku Pelajaran')->first();
        $rak1 = RakBuku::where('kode_rak', 'R-001')->first();
        $rak2 = RakBuku::where('kode_rak', 'R-002')->first();

        Buku::create(['kategori_id' => $novel->id, 'rak_buku_id' => $rak1->id, 'judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2005, 'stok' => 5]);
        Buku::create(['kategori_id' => $novel->id, 'rak_buku_id' => $rak1->id, 'judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Hasta Mitra', 'tahun_terbit' => 1980, 'stok' => 3]);
        Buku::create(['kategori_id' => $novel->id, 'rak_buku_id' => $rak1->id, 'judul' => 'Tenggelamnya Kapal Van Der Wijck', 'penulis' => 'Hamka', 'penerbit' => 'Balai Pustaka', 'tahun_terbit' => 1938, 'stok' => 4]);
        Buku::create(['kategori_id' => $pelajaran->id, 'rak_buku_id' => $rak2->id, 'judul' => 'Matematika Kelas XII', 'penulis' => 'Tim Kemendikbud', 'penerbit' => 'Kemendikbud', 'tahun_terbit' => 2024, 'stok' => 10]);
        Buku::create(['kategori_id' => $pelajaran->id, 'rak_buku_id' => $rak2->id, 'judul' => 'Bahasa Indonesia Kelas XII', 'penulis' => 'Tim Kemendikbud', 'penerbit' => 'Kemendikbud', 'tahun_terbit' => 2024, 'stok' => 8]);

        // === PENGATURAN ===
        Pengaturan::create(['kunci' => 'denda_per_hari', 'nilai' => '1000', 'deskripsi' => 'Denda keterlambatan per hari (Rupiah)']);
        Pengaturan::create(['kunci' => 'max_peminjaman', 'nilai' => '3', 'deskripsi' => 'Maksimal buku dipinjam per siswa']);
        Pengaturan::create(['kunci' => 'durasi_pinjam', 'nilai' => '7', 'deskripsi' => 'Durasi peminjaman default (hari)']);
        Pengaturan::create(['kunci' => 'nama_perpustakaan', 'nilai' => 'Perpustakaan Sekolah Digital', 'deskripsi' => 'Nama perpustakaan']);
    }
}
