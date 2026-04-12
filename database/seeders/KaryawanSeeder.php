<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 5,
                'nama' => 'kar1',
                'noPegawai' => '0001',
                'kontak' => 'kontak1',
                'alamat' => 'alamat1',
            ],
            [
                'user_id' => 5,
                'nama' => 'kar2',
                'noPegawai' => '0002',
                'kontak' => 'kontak2',
                'alamat' => 'alamat2',
            ],
            [
                'user_id' => 5,
                'nama' => 'kar3',
                'noPegawai' => '0003',
                'alamat' => 'alamat3',
                'kontak' => 'kontak3',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Karyawan::create($item);
        }
    }
}
