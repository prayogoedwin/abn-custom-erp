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
        Schema::create('bon_pihak3s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pihak3_id')->constrained('pihak3s');
            $table->string('metode')->nullable(); // Metode pembayaran (e.g., tunai, transfer)
            $table->string('keterangan')->nullable();
            $table->integer('masuk')->default(0);
            $table->integer('keluar')->default(0);
            // $table->integer('sisa')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_pihak3s');
    }
};
