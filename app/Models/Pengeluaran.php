<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kategori_pengeluaran_id',
        'nama_pengeluaran',
        'tanggal',
        'nominal',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function kategoriPengeluaran()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }
}
