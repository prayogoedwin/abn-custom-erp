<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimpanPinjamSupplier extends Model
{
    //
    //          $table->id();
    //         $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
    //         $table->string('tipe')->default('IN');
    //         $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
    //         $table->integer('nominal')->default(0);
    //         $table->string('keterangan')->nullable();
    //         $table->boolean('isactive')->nullable()->default(true);
    //         $table->timestamps();
    //         $table->string('deleted_at')->nullable();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    protected $fillable = [
        'supplier_id',
        'tipe',
        'pembelian_id',
        'nominal',
        'keterangan',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
}
