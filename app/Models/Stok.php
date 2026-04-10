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

    protected $fillable = [
        'produk_id',
        'tipe_stok',
        'satuan',
        'stok',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
