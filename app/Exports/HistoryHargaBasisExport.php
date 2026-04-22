<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\DinamisVariable;
use App\Models\HistoryHargaBasis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HistoryHargaBasisExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return HistoryHargaBasis::where('is_active', true)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Satuan',
            'Harga Basis Pembelian',
            'Tanggal Perubahan',
        ];
    }

    public function map($historyHargaBasis): array
    {
        return [
            $historyHargaBasis->id,
            $historyHargaBasis->produk->nama_produk,
            $historyHargaBasis->satuan,
            $historyHargaBasis->harga_basis,

            $historyHargaBasis->tanggal->format('d-m-Y'),

        ];
    }
}
