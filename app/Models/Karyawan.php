<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

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

    protected static function booted()
    {
        static::creating(function ($karyawan) {


            $user = User::create(
                [
                    'name' => $karyawan['nama'],
                    'email' => $karyawan['email'],
                    'password' => Hash::make($karyawan['password']),
                ]
            );

            $karyawan->user_id = $user->id;

            unset($karyawan->email);
            unset($karyawan->password);

            $lastKaryawan = self::orderBy('id', 'desc')->first();

            if (!$lastKaryawan) {

                $karyawan->noPegawai = '0001';
            } else {

                $lastNumber = (int) $lastKaryawan->noPegawai;
                $nextNumber = $lastNumber + 1;

                $karyawan->noPegawai = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
