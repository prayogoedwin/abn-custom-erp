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

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
