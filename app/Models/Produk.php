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
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        // Terjadi saat stok diupdate di database
        static::updated(function ($produk) {
            if (auth()->check()) {
                $produk->updated_by = auth()->id();
            }
            // Cek apakah kolom 'stok_akhir' yang berubah
            if ($produk->isDirty('stok_akhir')) {
                $stokLama = $produk->getOriginal('stok_akhir');
                $stokBaru = $produk->stok_akhir;
                $selisih = $stokBaru - $stokLama;

                // Simpan riwayat ke tabel mutasi
                Stok::create([
                    'produk_id' => $produk->id,
                    'tipe_stok' => $selisih > 0 ? 'masuk' : 'keluar',
                    'satuan' => $produk->satuan,
                    'stok' => abs($selisih),
                ]);
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
        return $this->belongsTo(KategoriProduk::class);
    }
}
