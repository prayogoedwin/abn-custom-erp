<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     */

    // protected $fillable = [
    //     'kategori_produk_id',
    //     'nama_produk',
    //     'satuan',
    //     'harga_basis_pembelian',
    //     'stok_akhir',
    //     'isactive',
    // ];
    public function run(): void
    {
        $data = [
            [
                'kategori_produk_id' => 1,
                'nama_produk' => 'Kopi',
                'satuan' => 'kg',
                'harga_basis_pembelian' => 10000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 1,
                'nama_produk' => 'Lada',
                'satuan' => 'kg',
                'harga_basis_pembelian' => 12000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],
            
            
            

        ];

        foreach ($data as $item) {
            \App\Models\Produk::create($item);
        }
    }
}
