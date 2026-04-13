<?php

namespace App\Exports;

use App\Models\Stok;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StokExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        return Stok::all();
    }

    public function headings(): array
    {
        return [
            'Produk',
            'Tipe',
            'Satuan',
            'Stok'
        ];
    }

    public function map($stok): array
    {
        return [
            $stok->produk->nama_produk,
            $stok->tipe_stok,
            $stok->satuan,
            $stok->stok,
        ];
    }
}
