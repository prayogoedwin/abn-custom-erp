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
