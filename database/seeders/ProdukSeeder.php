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
                'kategori_produk_id' => 6,
                'nama_produk' => 'Kopi',
                'satuan' => 'ton',
                'harga_basis_pembelian' => 1500000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 6,
                'nama_produk' => 'Lada',
                'satuan' => 'ton',
                'harga_basis_pembelian' => 1200000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 1,
                'nama_produk' => 'Laptop',
                'satuan' => 'unit',
                'harga_basis_pembelian' => 10000000,
                'stok_akhir' => 50,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 3,
                'nama_produk' => 'Cokelat Batangan',
                'satuan' => 'pcs',
                'harga_basis_pembelian' => 20000,
                'stok_akhir' => 150,
                'isactive' => true,
            ],
            

        ];

        foreach ($data as $item) {
            \App\Models\Produk::create($item);
        }
    }
}
