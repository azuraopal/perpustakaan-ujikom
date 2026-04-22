<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->foreignId('rak_buku_id')->nullable()->constrained('rak_bukus')->nullOnDelete();
            $table->string('isbn', 20)->unique()->nullable();
            $table->string('judul', 255);
            $table->string('penulis', 150);
            $table->string('penerbit', 150);
            $table->smallInteger('tahun_terbit');
            $table->integer('stok')->default(0);
            $table->text('sinopsis')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->timestamps();

            $table->index('kategori_id');
            $table->index('judul');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
