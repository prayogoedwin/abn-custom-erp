<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    // Schema::create('produks', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('kategori_produk_id')->constrained()->cascadeOnDelete();
    //         $table->string('nama_produk');
    //         $table->string('satuan');
    //         $table->decimal('harga_basis_pembelian', 15, 2);
    //         $table->integer('stok_akhir');
    //         $table->boolean('isactive')->default(true);

    //         $table->timestampscustom();
    //     });
    protected $fillable = [
        'kategori_produk_id',
        'nama_produk',
        'satuan',
        'harga_basis_pembelian',
        'stok_akhir',
        'isactive',
    ];

    public function kategori(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class);
    }
}
