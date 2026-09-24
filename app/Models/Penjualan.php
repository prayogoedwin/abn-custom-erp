<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Penjualan extends Model
{
    use SoftDeletes;
    // Schema::create('penjualans', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('no_transaksi_penjualan');
    //         $table->foreignId('pengiriman_id')->constrained('pengirimans');
    //         $table->foreignId('customer_id')->constrained('customers');
    //         $table->timestamps();
    //         $table->softDeletes();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });

    protected $fillable = [
        'no_transaksi_penjualan',
        'pengiriman_id',
        'customer_id',

        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tanggal()
    {
        return $this->created_at->translatedFormat('d M Y');
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function detailsJual()
    {
        return $this->details->filter(fn (PenjualanDetail $detail) => $detail->isJual());
    }

    public function invoiceJumlah(): int
    {
        return (int) $this->detailsJual()->sum('sub_total');
    }

    public function invoicePpn(): int
    {
        return (int) round($this->invoiceJumlah() * 0.011);
    }

    public function invoicePph22(): int
    {
        return (int) round($this->invoiceJumlah() * 0.0025);
    }

    public function invoiceTotalDibayar(): int
    {
        return $this->invoiceJumlah() + $this->invoicePpn() - $this->invoicePph22();
    }
}
