<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('ambil_tunai')->after('tipe_transaksi_pembelian')->nullable();
            $table->string('ambil_transfer')->after('tipe_transaksi_pembelian')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('ambil_tunai');
            $table->dropColumn('ambil_transfer');
        });
    }
};
