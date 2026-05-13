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

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
