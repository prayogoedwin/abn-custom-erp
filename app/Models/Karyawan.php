<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'noPegawai',
        'kontak',
        'alamat',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
