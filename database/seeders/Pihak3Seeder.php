<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Pihak3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            
            [
                'nama' => 'PT. Global Logistik Sejahtera',
                'kontak' => '021-5566778',
                'alamat' => 'Kawasan Industri Jababeka Blok C-14, Cikarang',
            ],
            [
                'nama' => 'UD. Sumber Makmur',
                'kontak' => '0813-9988-1122',
                'alamat' => 'Jl. Pasar Atom No. 45, Surabaya',
            ],
            [
                'nama' => 'PT. Tekno Utama Indonesia',
                'kontak' => '021-8899001',
                'alamat' => 'Gedung Cyber 2 Lantai 10, Kuningan, Jakarta Selatan',
            ],
            [
                'nama' => 'Toko Bangunan Jaya Abadi',
                'kontak' => '0852-1122-3344',
                'alamat' => 'Jl. Ahmad Yani No. 102, Bandung',
            ],
            [
                'nama' => 'CV. Agro Bakti Nusantara',
                'kontak' => '0274-445566',
                'alamat' => 'Jl. Kaliurang KM 7, Sleman, Yogyakarta',
            ],
            [
                'nama' => 'PT. Sinar Surya Elektrik',
                'kontak' => '024-778899',
                'alamat' => 'Kawasan Industri Gatot Subroto, Semarang',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Pihak3::create($item);
        }
    }
}
