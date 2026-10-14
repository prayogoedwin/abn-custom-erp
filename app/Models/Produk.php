<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'kategori_produk_id',
        'nama_produk',
        'satuan',
        'harga_basis_pembelian',
        'harga_basis_penjualan',
        'stok_akhir',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::created(function ($produk) {

            Stok::create([
                'produk_id' => $produk->id,
                'tipe_stok' =>  'Masuk',
                'satuan' => $produk->satuan,
                'stok' => $produk->stok_akhir,
            ]);

            HistoryHargaBasis::create([
                'produk_id' => $produk->id,
                'satuan' => $produk->satuan,
                'harga_basis' => $produk->harga_basis_pembelian,
                'tanggal' => now(),
            ]);
        });



        // Terjadi update di database
        static::updated(function ($produk) {
            if (auth()->check()) {
                $produk->updated_by = auth()->id();
            }
            
            // Cek apakah kolom 'harga_basis_pembelian' yang berubah
            if ($produk->isDirty('harga_basis_pembelian')) {
                $old_harga = $produk->getOriginal('harga_basis_pembelian');
                $new_harga = $produk->harga_basis_pembelian;

                if ($new_harga != $old_harga) {
                    HistoryHargaBasis::create([
                        'produk_id' => $produk->id,
                        'satuan' => $produk->satuan,
                        'harga_basis' => $new_harga,
                        'tanggal' => now(),
                    ]);
                }
            }
        });
    }


    public function kategori(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }
}
