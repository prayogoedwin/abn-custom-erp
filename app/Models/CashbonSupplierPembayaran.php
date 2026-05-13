<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbonSupplierPembayaran extends Model
{

    protected $fillable = [
        'supplier_id',
        'nominal_bayar',
        'tipe',
        'keterangan',

        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
