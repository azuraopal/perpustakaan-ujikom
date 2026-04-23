<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dendas', function (Blueprint $table) {
            $table->string('metode_pembayaran', 20)->nullable()->after('status_bayar');
            $table->string('midtrans_snap_token')->nullable()->after('metode_pembayaran');
            $table->string('midtrans_order_id', 50)->nullable()->after('midtrans_snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('dendas', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'midtrans_snap_token', 'midtrans_order_id']);
        });
    }
};
