<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // Ambil ID pembelian yang sudah di-soft delete
            $penjualanids = DB::table('penjualans')
                ->whereNotNull('deleted_at')
                ->pluck('id');

            if ($penjualanids->isEmpty()) {
                return;
            }

            // Ambil ID detail pembelian terkait
            $detailIds = DB::table('penjualan_details')
                ->whereIn('penjualan_id', $penjualanids)
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($detailIds->isEmpty()) {
                return;
            }

            // Soft delete detail pembelian
            DB::table('penjualan_details')
                ->whereIn('id', $detailIds)
                ->update([
                    'deleted_at' => $now,
                    'updated_at' => $now,
                ]);


            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
