<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    //
    // Schema::create('absensis', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
    //         $table->string('nama')->default('');
    //         $table->string('bulan')->default('');
    //         $table->string('tahun')->default('');
    //         $table->integer('jumlah_masuk')->default(0);
    //         $table->integer('jumlah_absen')->default(0);
    //         $table->integer('jumlah_izin')->default(0);
    //         $table->boolean('isactive')->nullable()->default(true);
    //         $table->timestamps();
    //         $table->string('deleted_at')->nullable();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });

    protected $fillable = [
        'karyawan_id',
        'nama',
        'bulan',
        'tahun',
        'jumlah_masuk',
        'jumlah_absen',
        'jumlah_izin',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    
}
