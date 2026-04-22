<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PembelianExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pembelian::with('supplier')->where('isactive', true)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Transaksi',
            'Supplier',
            'Nopol',
            'Tipe Transaksi',
            'Total Nominal Pembelian',
            'Total Nominal Terbayar',
            'Kekurangan',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Tipe Pembayaran',
            'Created At',
            'Updated At',
        ];
    }

    public function map($pembelian): array
    {
        return [
            $pembelian->id,
            $pembelian->no_transaksi,
            $pembelian->supplier->nama,
            $pembelian->nopol,
            $pembelian->tipe_transaksi_pembelian,
            $pembelian->total_nominal_pembelian,
            $pembelian->total_nominal_terbayar,
            $pembelian->kekurangan,
            $pembelian->status_pembayaran,
            $pembelian->metode_pembayaran,
            $pembelian->tipe_pembayaran,
            $pembelian->created_at->format('d-m-Y H:i:s'),
            $pembelian->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
