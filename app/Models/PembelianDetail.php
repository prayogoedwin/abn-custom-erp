<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    //
    // $table->id();
    // $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
    // $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
    // $table->float('netto')->nullable();
    // $table->string('satuan');
    // $table->float('rendeman');
    // $table->integer('bobot')->default(0);
    // $table->integer('harga')->default(0);
    // $table->integer('harga_basis')->nullable();
    // $table->integer('harga_basis_pembelian')->nullable();
    // $table->integer('harga_netto')->nullable();

    // $table->boolean('isactive')->nullable()->default(true);
    // $table->timestamps();
    // $table->string('deleted_at')->nullable();
    // $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    // $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    // $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    protected $fillable = [
        'pembelian_id',
        'produk_id',
        'netto',
        'satuan',
        'rendeman',
        'bobot',
        'harga',
        'harga_basis',
        'harga_basis_pembelian',
        'harga_netto',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::created(function ($pembelianDetail) {

            Stok::create([
                'produk_id' => $pembelianDetail->produk_id,
                'tipe_stok' =>  'Masuk',
                'satuan' => $pembelianDetail->satuan,
                'stok' => $pembelianDetail->netto,
            ]);

        });



        // Terjadi update di database
        static::updated(function ($pembelianDetail) {
            if (auth()->check()) {
                $pembelianDetail->updated_by = auth()->id();
            }
            // Cek apakah kolom 'netto' yang berubah
            if ($pembelianDetail->isDirty('netto')) {
                $nettoLama = $pembelianDetail->getOriginal('netto');
                $nettoBaru = $pembelianDetail->netto;
                $selisih = $nettoBaru - $nettoLama;

                // Simpan riwayat ke tabel mutasi
                Stok::create([
                    'produk_id' => $pembelianDetail->produk->id,
                    'tipe_stok' => $selisih > 0 ? 'Masuk' : 'Keluar',
                    'satuan' => $pembelianDetail->produk->satuan,
                    'stok' => abs($selisih),
                ]);
            }
        });
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
