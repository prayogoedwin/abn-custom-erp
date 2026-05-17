<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    //
    // Schema::create('penjualan_details', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('pengiriman_detail_id')->constrained('pengiriman_details');
    //         $table->foreignId('produk_id')->constrained('produks');
    //         $table->string('tipe'); // (Titip/Jual)
    //         $table->float('netto_pengiriman'); // ambil dari pengiriman detail
    //         $table->float('netto'); 
    //         $table->float('selisih'); 
    //         $table->integer('basis_harga'); 
    //         $table->integer('sub_total'); //  (netto * basis harga) 
    //         $table->integer('pph'); 
    //         $table->integer('ppn'); 
    //         $table->integer('nominal_akhir'); 
    //         $table->timestamps();
    //         $table->softDeletes();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });

    protected $fillable = [
        'penjualan_id',
        'pengiriman_detail_id',
        'produk_id',
        'tipe',
        'netto_pengiriman',
        'netto',
        'selisih',
        'basis_harga',
        'sub_total',
        'pph',
        'ppn',
        'nominal_akhir',

        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function pengiriman_detail()
    {
        return $this->belongsTo(PengirimanDetail::class);
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

}
