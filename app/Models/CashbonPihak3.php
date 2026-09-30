<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbonPihak3 extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $table = 'cashbon_pihak3s';

    protected $fillable = [
        'pihak3_id',
        'nominal_cashbon',
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
