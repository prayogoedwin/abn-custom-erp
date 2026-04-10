<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Elektronik'],
            ['nama' => 'Pakaian'],
            ['nama' => 'Makanan'],
            ['nama' => 'Minuman'],
            ['nama' => 'Peralatan Rumah Tangga'],
            ['nama' => 'Bahan Makanan'],
            ['nama' => 'Kendaraan'],
            ['nama' => 'Alat Tulis'],
            ['nama' => 'Mainan'],
            ['nama' => 'Peralatan Olahraga'],
        ];

        foreach ($data as $item) {
            \App\Models\KategoriProduk::create($item);
        }
    }
}
