<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_denda', 20);
            $table->integer('jumlah_hari')->nullable();
            $table->decimal('nominal', 12, 2);
            $table->string('status_bayar', 20)->default('belum_bayar');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
