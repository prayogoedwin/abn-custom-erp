<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbilSupplier extends Model
{
    use SoftDeletes;
    protected $table = 'ambil_suppliers';

    protected $fillable = [
        'supplier_id',
        'nominal_ambil',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
