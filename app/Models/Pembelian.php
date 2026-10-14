<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    

    protected $table = 'pembelians';

    protected $fillable = [
        'no_transaksi',
        'supplier_id',
        'nopol',
        'tipe_transaksi_pembelian',
        'ambil_transfer',
        'ambil_tunai',
        'total_nominal_pembelian',
        'total_nominal_terbayar',
        'kekurangan',
        'status_pembayaran',
        'metode_pembayaran',
        'tipe_pembayaran',
        'keterangan',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::creating(function ($pembelian) {
            // Placeholder sementara, akan diganti setelah ID terbentuk.
            $pembelian->no_transaksi = 'TRX-TMP-' . now()->format('YmdHis');

            // Optional: Ensure 'created_by' is set to the logged-in user
            if (auth()->check()) {
                $pembelian->created_by = auth()->id();
            }
        });

        static::created(function ($pembelian) {
            $date = $pembelian->created_at?->format('Ymd') ?? now()->format('Ymd');
            $transactionNumber = "TRX-{$date}-" . str_pad((string) $pembelian->id, 6, '0', STR_PAD_LEFT);

            $pembelian->updateQuietly([
                'no_transaksi' => $transactionNumber,
            ]);
        });
    }

    public function tanggal()
    {
        return $this->created_at->translatedFormat('d M Y');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function titipSupplier()
    {
        return $this->hasOne(TitipSupplier::class);
    }
}
