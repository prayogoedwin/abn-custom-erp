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
        // Ambil data cashbon yang masuk
        $cashbon = DB::table('cashbon_suppliers')
            ->select([
                'supplier_id',
                'tipe',
                'keterangan',
                'nominal_cashbon',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->get()
            ->map(function ($row) {
                return [
                    'supplier_id' => $row->supplier_id,
                    'metode'      => $row->tipe,
                    'keterangan'  => $row->keterangan,
                    'masuk'       => $row->nominal_cashbon,
                    'keluar'      => 0,
                    'created_at'  => $row->created_at,
                    'updated_at'  => $row->updated_at,
                    'deleted_at'  => $row->deleted_at,
                ];
            });

        // Ambil data pembayaran cashbon
        $pembayaran = DB::table('cashbon_supplier_pembayarans')
            ->select([
                'supplier_id',
                'tipe',
                'keterangan',
                'nominal_bayar',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->get()
            ->map(function ($row) {
                return [
                    'supplier_id' => $row->supplier_id,
                    'metode'      => $row->tipe,
                    'keterangan'  => $row->keterangan,
                    'masuk'       => 0,
                    'keluar'      => $row->nominal_bayar,
                    'created_at'  => $row->created_at,
                    'updated_at'  => $row->updated_at,
                    'deleted_at'  => $row->deleted_at,
                ];
            });

        // Gabungkan kedua tabel dan urutkan berdasarkan created_at
        $data = $cashbon
            ->concat($pembayaran)
            ->sort(function ($a, $b) {
                // created_at NULL diletakkan paling awal
                $dateA = $a['created_at'] ?? '';
                $dateB = $b['created_at'] ?? '';

                return strcmp($dateA, $dateB);
            })
            ->values();

        // Insert berurutan agar ID auto increment mengikuti urutan waktu
        foreach ($data->chunk(500) as $chunk) {
            DB::table('bon_suppliers')->insert(
                $chunk->all()
            );
        }
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bon_suppliers', function (Blueprint $table) {
            //
        });
    }
};
