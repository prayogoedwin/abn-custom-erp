<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('isactive');
        });

        //masukan created_at di tanggal ke data yang sudah terlanjut dibuat
        DB::table('pembelians')->update(['tanggal' => DB::raw('created_at')]);

        Schema::table('pengirimans', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('no_transaksi');
        });

        //masukan created_at di tanggal ke data yang sudah terlanjut dibuat
        DB::table('pengirimans')->update(['tanggal' => DB::raw('created_at')]);

        Schema::table('penjualans', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('customer_id');
        });

        //masukan created_at di tanggal ke data yang sudah terlanjut dibuat
        DB::table('penjualans')->update(['tanggal' => DB::raw('created_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
