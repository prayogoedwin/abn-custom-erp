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
        Schema::table('pembelian_details', function (Blueprint $table) {
            $table->string('tipe_transaksi_pembelian')->after('produk_id')->nullable(); // titip or jual
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian_details', function (Blueprint $table) {
            //
        });
    }
};
