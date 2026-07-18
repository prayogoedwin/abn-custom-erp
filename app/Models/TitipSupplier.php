<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitipSupplier extends Model
{
    protected $table = 'titip_suppliers';

    protected $fillable = [
        'supplier_id',
        'pembelian_id',
        'nominal_titip',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}
