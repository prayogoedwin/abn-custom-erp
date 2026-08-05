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
        'bobot',
        'rendeman',
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

    protected static function booted()
    {
        static::created(function ($detail) {
            // Otomatis tambah data ke table Stok dengan tipe 'OUT' saat detail penjualan dibuat
            if ($detail->tipe == 'jual') {
                Stok::create([
                    'produk_id'  => $detail->produk_id,
                    'tipe_stok'       => 'Keluar',
                    'satuan' => $detail->produk->satuan,
                    'stok'     => $detail->netto,
                    'penjualan_detail_id' => $detail->id,
                    'created_by' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($detail) {
            // Cek apakah kolom jumlah (netto) atau produk_id mengalami perubahan
            if ($detail->wasChanged('netto') || $detail->wasChanged('produk_id')) {

                $stokLama = $detail->getOriginal('netto');
                $stokBaru = $detail->netto;
                $selisih  = $stokBaru - $stokLama;

                if ($selisih > 0) {
                    // Jika stok baru lebih besar, berarti barang keluar tambah banyak
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe_stok'       => 'Keluar',
                        'satuan' => $detail->produk->satuan,

                        'stok'     => $selisih,
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                } elseif ($selisih < 0) {
                    // Jika stok baru lebih kecil, kembalikan kelebihannya ke gudang (Masuk)
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe_stok'       => 'Masuk',
                        'satuan' => $detail->produk->satuan,

                        'stok'     => abs($selisih), // Mengubah nilai negatif menjadi positif
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                }
            }
        });
    }

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
