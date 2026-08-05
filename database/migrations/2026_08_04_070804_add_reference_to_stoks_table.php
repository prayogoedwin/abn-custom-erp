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
        Schema::table('stoks', function (Blueprint $table) {
            $table->foreignId('pembelian_detail_id')->nullable()->constrained('pembelian_details')->cascadeOnDelete();
            $table->foreignId('penjualan_detail_id')->nullable()->constrained('penjualan_details')->cascadeOnDelete();
            $table->foreignId('pengiriman_detail_id')->nullable()->constrained('pengiriman_details')->cascadeOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
            $table->dropForeign(['pembelian_detail_id']);
            $table->dropForeign(['penjualan_detail_id']);
            $table->dropForeign(['pengiriman_detail_id']);
            $table->dropColumn(['pembelian_detail_id', 'penjualan_detail_id', 'pengiriman_detail_id']);
        });
    }
};
