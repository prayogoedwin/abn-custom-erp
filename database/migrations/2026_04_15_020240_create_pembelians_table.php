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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi');

            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('nopol')->nullable();
            $table->string('tipe_transaksi_pembelian')->default('Titip'); // titip or jual
            $table->integer('total_nominal_pembelian')->default(0);
            $table->integer('total_nominal_terbayar')->default(0);
            $table->integer('kekurangan')->nullable();
            $table->string('status_pembayaran')->default('Lunas'); // lunas or belum lunas
            $table->string('metode_pembayaran')->nullable(); // transfer / cash
            $table->string('tipe_pembayaran')->nullable(); // potong / full / titip

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
        Schema::dropIfExists('pembelians');
    }
};
