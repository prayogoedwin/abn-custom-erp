<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokTitipan extends Model
{
    protected $table = 'stok_titipans';

    //  Schema::create('stok_titipans', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
    //         $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    //         $table->string('tipe_stok'); // masuk / keluar
    //         $table->string('satuan');
    //         $table->float('jumlah')->default(0);
    //         $table->string('keterangan')->nullable();

    //         $table->softDeletes();
    //         $table->timestamps();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });
    // }
    protected $fillable = [
        'produk_id',
        'supplier_id',
        'pembelian_id',
        'tipe_stok',
        'satuan',
        'jumlah',
        'rendeman',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public static function sisaUntuk(int $supplierId, int $produkId, ?int $kecualiPembelianId = null): float
    {
        $masuk = (float) static::query()
            ->where('supplier_id', $supplierId)
            ->where('produk_id', $produkId)
            ->whereRaw('LOWER(tipe_stok) = ?', ['masuk'])
            ->sum('jumlah');

        $keluarQuery = static::query()
            ->where('supplier_id', $supplierId)
            ->where('produk_id', $produkId)
            ->whereRaw('LOWER(tipe_stok) = ?', ['keluar']);

        if ($kecualiPembelianId) {
            $keluarQuery->where(function ($inner) use ($kecualiPembelianId) {
                $inner->whereNull('pembelian_id')
                    ->orWhere('pembelian_id', '!=', $kecualiPembelianId);
            });
        }

        $keluar = (float) $keluarQuery->sum('jumlah');

        return $masuk - $keluar;
    }

    /**
     * @return array<string, float>
     */
    public static function petaSisa(): array
    {
        return static::query()
            ->selectRaw("supplier_id, produk_id, SUM(CASE WHEN LOWER(tipe_stok) = 'masuk' THEN jumlah WHEN LOWER(tipe_stok) = 'keluar' THEN -jumlah ELSE 0 END) as sisa")
            ->groupBy('supplier_id', 'produk_id')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->supplier_id.'-'.$row->produk_id => (float) $row->sisa])
            ->all();
    }

    public function tanggal()
    {
        return $this->created_at->translatedFormat('d M Y');
    }
}
