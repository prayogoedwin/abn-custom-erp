<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pengiriman extends Model
{
    use SoftDeletes;
    // Schema::create('pengirimans', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('customer_id')->constrained('customers');
    //     $table->string('nopol');
    //     $table->no_transaksi('id_transaksi'); // id-nopo-random
    //     $table->timestamps();
    //     $table->softDeletes();
    //     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //     $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

    // });

    protected $table = "pengirimans";

    protected $fillable = [
        'customer_id',
        'nopol',
        'no_transaksi',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::created(function ($pengiriman) {
            $random = strtoupper(Str::random(5));
            $pengiriman->no_transaksi = "{$pengiriman->id}-{$pengiriman->nopol}-{$random}";
            $pengiriman->save();
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detail()
    {
        return $this->hasMany(PengirimanDetail::class);
    }
}
