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
            ['nama' => 'Hasil Bumi'],
            
        ];

        foreach ($data as $item) {
            \App\Models\KategoriProduk::create($item);
        }
    }
}
