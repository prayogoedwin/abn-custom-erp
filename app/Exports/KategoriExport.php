<?php

namespace App\Exports;

use App\Models\KategoriProduk;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KategoriExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return KategoriProduk::all();
    }

    public function headings(): array
    {
        return [
            'nama',
        ];
    }

    public function map($kategori): array
    {
        return [
            $kategori->nama,
        ];
    }
}
