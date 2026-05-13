<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbonKaryawan extends Model
{
    // Schema::create('cashbon_karyawans', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('karyawan_id')->constrained('karyawans');
    //     $table->integer('nominal_cashbon')->default(0);
    //     $table->string('tipe'); // Cash/Transfer
    //     $table->string('keterangan')->nullable();
    //     $table->timestamps();
    //     $table->softDeletes();
    //     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    // });

    protected $fillable = [
        'karyawan_id',
        'nominal_cashbon',
        'tipe',
        'keterangan',

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
