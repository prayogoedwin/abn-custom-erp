<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    //Schema::create('kategori_produks', function (Blueprint $table) {
    //     $table->id();
    //     $table->string('nama');
    //     $table->timestampscustom();
    // });
    protected $fillable = ['nama'];

    
}
