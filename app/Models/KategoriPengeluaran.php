<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriPengeluaran extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama_kategori',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_pengeluaran_id');
    }
}
