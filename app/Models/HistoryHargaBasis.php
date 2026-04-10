<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryHargaBasis extends Model
{
    //
    // // log perubahan harga produk
    // Schema::create('history_harga_bases', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
    //     $table->string('satuan');
    //     $table->decimal('harga_basis', 15, 2);
    //     $table->timestamp('tanggal');
    //     $table->timestamps();
    // });

    protected $fillable = [
        'produk_id',
        'satuan',
        'harga_basis',
        'tanggal',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
