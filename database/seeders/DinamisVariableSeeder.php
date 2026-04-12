<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DinamisVariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 'nama_variable',
        // 'jenis',
        // 'variable_value',
        // 'keterangan',

        $data = [
            [
                'nama_variable' => 'Tarak Kopi',
                'jenis' => 'Persen',
                'variable_value' => 5,
                'keterangan' => 'Tarak Untuk Produk Kopi',
            ],
            [
                'nama_variable' => 'Rendeman Lada, Persen',
                'jenis' => 'Persen',
                'variable_value' => 5,
                'keterangan' => 'Rendeman Untuk Produk Lada',
            ],
            [
                'nama_variable' => 'Bobot Lada, Nominal',
                'jenis' => 'Nominal',
                'variable_value' => -10000,
                'keterangan' => '(bisa bernilai + atau -)',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\DinamisVariable::create($item);
        }
    }
}
