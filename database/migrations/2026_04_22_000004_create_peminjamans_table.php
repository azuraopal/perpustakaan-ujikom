<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('buku_id')->constrained('bukus')->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_harus_kembali');
            $table->date('tanggal_dikembalikan')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('status', 20)->default('dipinjam');
            $table->string('kondisi_buku', 20)->nullable()->default('baik');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('buku_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
