<?php

namespace App\Exports;

use App\Models\PengirimanDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengirimanDetailExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PengirimanDetail::with('pengiriman')->where('deleted_at', null)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Transaksi Pembelian',
            'Produk',
            'Jumlah Per Karung',
            'Jumlah Karung',
            'Bruto',
            'Tara',
            'Netto',
            'Created At',
            'Updated At',
        ];
    }

    public function map($pembelianDetail): array
    {
        return [
            $pembelianDetail->id,
            $pembelianDetail->pembelian->no_transaksi,
            $pembelianDetail->nama_produk,
            $pembelianDetail->jumlah_per_karung,
            $pembelianDetail->jumlah_karung,
            $pembelianDetail->bruto,
            $pembelianDetail->tara,
            $pembelianDetail->netto,
            $pembelianDetail->created_at->format('d-m-Y H:i:s'),
            $pembelianDetail->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
