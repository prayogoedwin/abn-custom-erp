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
                'nama' => 'UD. Maju Bersama ATK',
                'kontak' => '0819-0011-2233',
                'alamat' => 'Jl. Pemuda No. 12, Medan',
            ],
            [
                'nama' => 'PT. Pangan Lestari Mandiri',
                'kontak' => '031-334455',
                'alamat' => 'Jl. Margomulyo Indah Blok B-5, Surabaya',
            ],
            [
                'nama' => 'CV. Grafika Kreatif Solusi',
                'kontak' => '022-667788',
                'alamat' => 'Jl. Lengkong Besar No. 88, Bandung',
            ],
            [
                'nama' => 'PT. Sentra Medika Distribusi',
                'kontak' => '021-445522',
                'alamat' => 'Kawasan Marunda Center Blok A-2, Bekasi',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Supplier::create($item);
        }
    }
}
