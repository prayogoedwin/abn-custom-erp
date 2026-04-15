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
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->float('netto')->nullable();
            $table->string('satuan');
            $table->float('rendeman');
            $table->integer('bobot')->default(0);
            $table->integer('harga')->default(0);
            $table->integer('harga_basis')->nullable();
            $table->integer('harga_basis_pembelian')->nullable();
            $table->integer('harga_netto')->nullable();
            
            $table->boolean('isactive')->nullable()->default(true);
            $table->timestamps();
            $table->string('deleted_at')->nullable();
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
        Schema::dropIfExists('pembelian_details');
    }
};
