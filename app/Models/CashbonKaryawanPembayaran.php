<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbonKaryawanPembayaran extends Model
{
    
    // Schema::create('cashbon_karyawan_pembayarans', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('karyawan_id')->constrained('karyawans');
    //     $table->integer('nominal_bayar')->default(0);
    //     $table->string('tipe'); // cash/transfer
    //     $table->string('keterangan')->nullable();
    //     $table->timestamps();
    //     $table->softDeletes();
    //     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    // });

    protected $fillable = [
        'karyawan_id',
        'nominal_bayar',
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
