<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Putri Rahayu',
                'kontak' => '081234567897',
                'alamat' => 'Jl. Tulip No. 7, Medan',
            ],
            [
                'nama' => 'Eko Prasetyo',
                'kontak' => '081234567898',
                'alamat' => 'Jl. Flamboyan No. 22, Makassar',
            ],
            [
                'nama' => 'Maya Indah',
                'kontak' => '081234567899',
                'alamat' => 'Jl. Sakura No. 11, Palembang',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Customer::create($item);
        }
    }
}
