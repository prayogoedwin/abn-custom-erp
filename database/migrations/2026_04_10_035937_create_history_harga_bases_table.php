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
        // log perubahan harga produk
        Schema::create('history_harga_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
            $table->string('satuan');
            $table->decimal('harga_basis', 15, 2);
            $table->timestamp('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_harga_bases');
    }
};
