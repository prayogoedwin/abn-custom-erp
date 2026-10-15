<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{

    protected $fillable = [
        'penjualan_id',
        'pengiriman_detail_id',
        'produk_id',
        'tipe',
        'netto_pengiriman',
        'netto',
        'bobot',
        'rendeman',
        'selisih',
        'basis_harga',
        'sub_total',
        'pph',
        'ppn',
        'nominal_akhir',

        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::created(function ($detail) {
            // Otomatis tambah data ke table Stok dengan tipe 'OUT' saat detail penjualan dibuat
            if ($detail->tipe == 'jual') {
                Stok::create([
                    'produk_id'  => $detail->produk_id,
                    'tipe_stok'       => 'Keluar',
                    'satuan' => $detail->produk->satuan,
                    'stok'     => $detail->netto,
                    'penjualan_detail_id' => $detail->id,
                    'created_by' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($detail) {
            // Cek apakah kolom jumlah (netto) atau produk_id mengalami perubahan
            if ($detail->wasChanged('netto') || $detail->wasChanged('produk_id')) {

                $stokLama = $detail->getOriginal('netto');
                $stokBaru = $detail->netto;
                $selisih  = $stokBaru - $stokLama;

                if ($selisih > 0) {
                    // Jika stok baru lebih besar, berarti barang keluar tambah banyak
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe_stok'       => 'Keluar',
                        'satuan' => $detail->produk->satuan,

                        'stok'     => $selisih,
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                } elseif ($selisih < 0) {
                    // Jika stok baru lebih kecil, kembalikan kelebihannya ke gudang (Masuk)
                    Stok::create([
                        'produk_id'  => $detail->produk_id,
                        'tipe_stok'       => 'Masuk',
                        'satuan' => $detail->produk->satuan,

                        'stok'     => abs($selisih), // Mengubah nilai negatif menjadi positif
                        'created_by' => auth()->id() ?? $detail->updated_by,
                    ]);
                }
            }
        });
    }

    public function pengiriman_detail()
    {
        return $this->belongsTo(PengirimanDetail::class);
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function isJual(): bool
    {
        return strtolower((string) $this->tipe) === 'jual';
    }

    public function isTitip(): bool
    {
        return strtolower((string) $this->tipe) === 'titip';
    }

    public function hargaUntukNota(): int
    {
        $netto = (float) $this->netto;
        if ($netto <= 0) {
            return (int) $this->basis_harga;
        }

        return (int) round(((int) $this->sub_total) / $netto);
    }
}
