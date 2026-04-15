<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    //
    // public function up(): void
    // {
    //     Schema::create('pembelians', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('no_transaksi');

    //         $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
    //         $table->string('nopol')->nullable();
    //         $table->string('tipe_transaksi_pembelian')->default('Titip'); // titip or jual
    //         $table->integer('total_nominal_pembelian')->default(0);
    //         $table->integer('total_nominal_terbayar')->default(0);
    //         $table->integer('kekurangan')->nullable();
    //         $table->string('status_pembayaran')->default('Lunas'); // lunas or belum lunas
    //         $table->boolean('isactive')->nullable()->default(true);
    //         $table->timestamps();
    //         $table->string('deleted_at')->nullable();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });
    // }

    protected $fillable = [
        'no_transaksi',
        'supplier_id',
        'nopol',
        'tipe_transaksi_pembelian',
        'total_nominal_pembelian',
        'total_nominal_terbayar',
        'kekurangan',
        'status_pembayaran',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::creating(function ($pembelian) {

            $date = now()->format('Ymd');
            $random = strtoupper(bin2hex(random_bytes(3))); // Generates a short 6-char unique string

            $pembelian->no_transaksi = "TRX-{$date}-{$random}";

            // Optional: Ensure 'created_by' is set to the logged-in user
            if (auth()->check()) {
                $pembelian->created_by = auth()->id();
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
