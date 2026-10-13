<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BonSupplier extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'bon_suppliers';

    protected $fillable = [
        'supplier_id',
        'metode',
        'keterangan',
        'masuk',
        'keluar',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function generateSisa()
    {
        if (!$this->exists || $this->id === null) {
            return 0;
        }

        $saldo = self::query()
            ->where('supplier_id', $this->supplier_id)
            ->where('id', '<=', $this->id)
            ->selectRaw(
                'COALESCE(SUM(masuk), 0) - COALESCE(SUM(keluar), 0) AS saldo'
            )
            ->value('saldo');

        

        return $saldo;
    }
}
