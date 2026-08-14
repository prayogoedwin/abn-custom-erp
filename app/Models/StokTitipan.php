<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokTitipan extends Model
{
    protected $table = 'stok_titipans';

    //  Schema::create('stok_titipans', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
    //         $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    //         $table->string('tipe_stok'); // masuk / keluar
    //         $table->string('satuan');
    //         $table->float('jumlah')->default(0);
    //         $table->string('keterangan')->nullable();

    //         $table->softDeletes();
    //         $table->timestamps();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });
    // }
    protected $fillable = [
        'produk_id',
        'supplier_id',
        'pembelian_id',
        'tipe_stok',
        'satuan',
        'jumlah',
        'rendeman',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}
