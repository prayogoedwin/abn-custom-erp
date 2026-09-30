<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pihak3_id',
        'kategori_pengeluaran_id',
        'nama_pengeluaran',
        'tanggal',
        'nominal',
        'metode_pembayaran',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function pihak3()
    {
        return $this->belongsTo(Pihak3::class, 'pihak3_id');
    }

    public function kategoriPengeluaran()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    
}
