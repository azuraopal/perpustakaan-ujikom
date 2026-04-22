<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rak_bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rak', 20)->unique();
            $table->string('nama', 100);
            $table->string('lokasi', 100)->nullable();
            $table->integer('kapasitas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rak_bukus');
    }
};
