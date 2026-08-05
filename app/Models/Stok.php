<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    //Schema::create('stoks', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
    //     $table->string('tipe_stok'); // masuk / keluar
    //     $table->string('satuan');

    //     $table->integer('stok')->default(0);

    //     $table->timestamps();
    // });

    // Schema::table('stoks', function (Blueprint $table) {
    //         $table->foreignId('pembelian_detail_id')->nullable()->constrained('pembelian_details')->cascadeOnDelete();
    //         $table->foreignId('penjualan_detail_id')->nullable()->constrained('penjualan_details')->cascadeOnDelete();
    //         $table->foreignId('pengiriman_detail_id')->nullable()->constrained('pengiriman_details')->cascadeOnDelete(); 
    //     });
    

    protected $fillable = [
        'produk_id',
        'tipe_stok',
        'satuan',
        'stok',
        'pembelian_detail_id',
        'penjualan_detail_id',
        'pengiriman_detail_id',
    ];

    protected static function booted()
    {
        static::created(function ($stok) {

            // auto recalculate stok_akhir in Produk
            $produk = Produk::find($stok->produk_id);
            $totalStokMasuk = Stok::where('produk_id', $stok->produk_id)->where('tipe_stok', 'Masuk')->sum('stok');
            $totalStokKeluar = Stok::where('produk_id', $stok->produk_id)->where('tipe_stok', 'Keluar')->sum('stok');
            $produk->stok_akhir = $totalStokMasuk - $totalStokKeluar;

            $produk->update(['stok_akhir' => $produk->stok_akhir]);

            
        });
    }


    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function pembelianDetail()
    {
        return $this->belongsTo(PembelianDetail::class);
    }

    public function penjualanDetail()
    {
        return $this->belongsTo(PenjualanDetail::class);
    }

    public function pengirimanDetail()
    {
        return $this->belongsTo(PengirimanDetail::class);
    }
}
