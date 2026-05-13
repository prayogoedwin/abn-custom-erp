<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    //
    // Schema::create('penjualans', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('no_transaksi_penjualan');
    //         $table->foreignId('pengiriman_id')->constrained('pengirimans');
    //         $table->foreignId('customer_id')->constrained('customers');
    //         $table->timestamps();
    //         $table->softDeletes();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });

    protected $fillable = [
        'no_transaksi_penjualan',
        'pengiriman_id',
        'customer_id',

        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
