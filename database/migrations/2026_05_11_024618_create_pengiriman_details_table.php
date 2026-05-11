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
        Schema::create('pengiriman_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengirimans');
            $table->string('nama_barang');
            $table->integer('jumlah_karung');
            $table->integer('bruto');
            $table->integer('tara');//(JUMLAH KARUNG X 0.3 KG )
            $table->integer('netto');
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
        Schema::dropIfExists('pengiriman_details');
    }
};
