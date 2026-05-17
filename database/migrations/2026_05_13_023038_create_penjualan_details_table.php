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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans');
            $table->foreignId('pengiriman_detail_id')->constrained('pengiriman_details');
            $table->foreignId('produk_id')->constrained('produks');
            $table->string('tipe'); // (Titip/Jual)
            $table->float('netto_pengiriman'); // ambil dari pengiriman detail
            $table->float('netto'); 
            $table->float('selisih'); 
            $table->integer('basis_harga'); 
            $table->integer('sub_total'); //  (netto * basis harga) 
            $table->integer('pph'); 
            $table->integer('ppn'); 
            $table->integer('nominal_akhir'); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
