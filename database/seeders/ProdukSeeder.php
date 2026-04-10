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
                'nama_produk' => 'Laptop',
                'satuan' => 'unit',
                'harga_basis_pembelian' => 10000000,
                'stok_akhir' => 50,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 2,
                'nama_produk' => 'Kaos Polos',
                'satuan' => 'pcs',
                'harga_basis_pembelian' => 50000,
                'stok_akhir' => 200,
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
            [
                'kategori_produk_id' => 4,
                'nama_produk' => 'Air Mineral',
                'satuan' => 'botol',
                'harga_basis_pembelian' => 5000,
                'stok_akhir' => 300,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 5,
                'nama_produk' => 'Panci Stainless Steel',
                'satuan' => 'unit',
                'harga_basis_pembelian' => 150000,
                'stok_akhir' => 80,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 6,
                'nama_produk' => 'Kopi',
                'satuan' => 'gram',
                'harga_basis_pembelian' => 10000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],
            [
                'kategori_produk_id' => 6,
                'nama_produk' => 'Lada',
                'satuan' => 'gram',
                'harga_basis_pembelian' => 10000,
                'stok_akhir' => 100,
                'isactive' => true,
            ],

        ];

        foreach ($data as $item) {
            \App\Models\Produk::create($item);
        }
    }
}
