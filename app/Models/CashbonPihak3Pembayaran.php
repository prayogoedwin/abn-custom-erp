<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbonPihak3Pembayaran extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $table = 'cashbon_pihak3_pembayarans';
    protected $fillable = [
        'pihak3_id',
        'nominal_bayar',
        'tipe',
        'keterangan',

        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function pihak3()
    {
        return $this->belongsTo(Pihak3::class, 'pihak3_id');
    }

}
