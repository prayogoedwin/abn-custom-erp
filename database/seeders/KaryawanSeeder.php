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
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            ],
            [
                'nama' => 'Siti Aminah',
                'email' => 'siti.aminah@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567891',
                'alamat' => 'Jl. Mawar No. 5, Bandung',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567892',
                'alamat' => 'Jl. Melati No. 12, Surabaya',
            ],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567893',
                'alamat' => 'Jl. Kenanga No. 8, Yogyakarta',
            ],
            [
                'nama' => 'Rian Hidayat',
                'email' => 'rian.hidayat@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567894',
                'alamat' => 'Jl. Anggrek No. 15, Semarang',
            ],
            [
                'nama' => 'Lani Wijaya',
                'email' => 'lani.wijaya@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567895',
                'alamat' => 'Jl. Dahlia No. 3, Malang',
            ],
            [
                'nama' => 'Andi Wijaya',
                'email' => 'andi.wijaya@gmail.com',
                'password' => '12345678',
                'kontak' => '081234567896',
                'alamat' => 'Jl. Kamboja No. 20, Bali',
            ],
            
        ];

        foreach ($data as $item) {
            \App\Models\Karyawan::create($item);
        }
    }
}
