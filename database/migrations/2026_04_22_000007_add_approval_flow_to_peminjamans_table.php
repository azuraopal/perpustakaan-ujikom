<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjamans ALTER COLUMN status TYPE VARCHAR(50)");

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->foreignId('disetujui_oleh')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable()->after('disetujui_oleh');

            $table->foreignId('ditolak_oleh')->nullable()->after('disetujui_pada')->constrained('users')->nullOnDelete();
            $table->timestamp('ditolak_pada')->nullable()->after('ditolak_oleh');
            $table->text('alasan_penolakan')->nullable()->after('ditolak_pada');

            $table->timestamp('pengembalian_diajukan_pada')->nullable()->after('tanggal_dikembalikan');
            $table->foreignId('pengembalian_diverifikasi_oleh')->nullable()->after('pengembalian_diajukan_pada')->constrained('users')->nullOnDelete();
            $table->timestamp('pengembalian_diverifikasi_pada')->nullable()->after('pengembalian_diverifikasi_oleh');
            $table->text('catatan_pengembalian')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropConstrainedForeignId('ditolak_oleh');
            $table->dropConstrainedForeignId('pengembalian_diverifikasi_oleh');

            $table->dropColumn([
                'disetujui_pada',
                'ditolak_pada',
                'alasan_penolakan',
                'pengembalian_diajukan_pada',
                'pengembalian_diverifikasi_pada',
                'catatan_pengembalian',
            ]);
        });

        DB::statement("ALTER TABLE peminjamans ALTER COLUMN status TYPE VARCHAR(20)");
    }
};
