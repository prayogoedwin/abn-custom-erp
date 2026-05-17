<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanDetail extends Model
{
    //
    // Schema::create('pengiriman_details', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('pengiriman_id')->constrained('pengirimans');
    //     $table->string('nama_barang');
    //     $table->integer('jumlah_karung');
    //     $table->integer('bruto');
    //     $table->integer('tara');//(JUMLAH KARUNG X 0.3 KG )
    //     $table->integer('netto');
    //     $table->timestamps();
    //     $table->softDeletes();
    //     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        
    // });

    protected $fillable = [
        'pengiriman_id',
        'produk_id',
        'nama_barang',
        'jumlah_per_karung',
        'jumlah_karung',
        'bruto',
        'tara', //(JUMLAH KARUNG X 0.3 KG )
        'netto',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::created(function ($detail) {
            // Otomatis tambah data ke table Stok dengan tipe 'OUT' saat detail penjualan dibuat
            Stok::create([
                'produk_id'  => $detail->produk_id,
                'tipe'       => 'Keluar',
                'jumlah'     => $detail->jumlah, 
                'created_by' => auth()->id(),
            ]);
        });

        static::updated(function ($detail) {
            // Cek apakah kolom jumlah (netto) atau produk_id mengalami perubahan
            if ($detail->wasChanged('jumlah') || $detail->wasChanged('produk_id')) {
                
                $stokLama = $detail->getOriginal('jumlah');
                $stokBaru = $detail->jumlah;
                $selisih  = $stokBaru - $stokLama;

                if ($selisih > 0) {
                    // Jika stok baru lebih besar, berarti barang keluar tambah banyak
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe'       => 'Keluar',
                        'jumlah'     => $selisih,
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                } elseif ($selisih < 0) {
                    // Jika stok baru lebih kecil, kembalikan kelebihannya ke gudang (Masuk)
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe'       => 'Masuk',
                        'jumlah'     => abs($selisih), // Mengubah nilai negatif menjadi positif
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                }
            }
        });
    }

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
