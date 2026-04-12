<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
            'nama' => 'kar1',
            'kontak' => 'kontak1',
            'alamat' => 'alamat1',
            ],
            [
            'nama' => 'kar2',
            'kontak' => 'kontak2',
            'alamat' => 'alamat2',
            ],
            [
            'nama' => 'kar3',
            'alamat' => 'alamat3',
            'kontak' => 'kontak3',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Supplier::create($item);
        }
    }
}
