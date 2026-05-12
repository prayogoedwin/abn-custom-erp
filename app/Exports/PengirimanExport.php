<?php

namespace App\Exports;

use App\Models\Pengiriman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengirimanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pengiriman::with('customer')->where('deleted_at', null)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Transaksi Pengiriman',
            'Cutomer',
            'Nopol',
            'Created At',
            'Updated At',
        ];
    }

    public function map($pengiriman): array
    {
        return [
            $pengiriman->id,
            $pengiriman->no_transaksi,
            $pengiriman->customer->nama,
            $pengiriman->nopol,
            $pengiriman->created_at->format('d-m-Y H:i:s'),
            $pengiriman->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
