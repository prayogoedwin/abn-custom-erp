<?php

namespace App\Exports;

use App\Models\Stok;
use Maatwebsite\Excel\Concerns\FromCollection;

class StokExport implements FromCollection
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
