<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonPihak3 extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'bon_pihak3s';

    protected $fillable = [
        'pihak3_id',
        'metode',
        'keterangan',
        'masuk',
        'keluar',
    ];

    public function pihak3()
    {
        return $this->belongsTo(Pihak3::class);
    }

    public function generateSisa()
    {
        if (!$this->exists || $this->id === null) {
            return 0;
        }

        $saldo = self::query()
            ->where('pihak3_id', $this->pihak3_id)
            ->where('id', '<=', $this->id)
            ->selectRaw(
                'COALESCE(SUM(masuk), 0) - COALESCE(SUM(keluar), 0) AS saldo'
            )
            ->value('saldo');

        

        return $saldo;
    }
}
