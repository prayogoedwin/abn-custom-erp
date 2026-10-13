<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonKaryawan extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'bon_karyawans';

    protected $fillable = [
        'karyawan_id',
        'metode',
        'keterangan',
        'masuk',
        'keluar',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function generateSisa()
    {
        if (!$this->exists || $this->id === null) {
            return 0;
        }

        $saldo = self::query()
            ->where('karyawan_id', $this->karyawan_id)
            ->where('id', '<=', $this->id)
            ->selectRaw(
                'COALESCE(SUM(masuk), 0) - COALESCE(SUM(keluar), 0) AS saldo'
            )
            ->value('saldo');

        

        return $saldo;
    }
}
