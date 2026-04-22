<?php

namespace App\Exports;

use App\Models\PembelianDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PembelianDetailExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PembelianDetail::with('pembelian', 'produk')->where('isactive', true)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Transaksi Pembelian',
            'Produk',
            'Netto',
            'Satuan',
            'Rendeman',
            'Bobot',
            'Harga',
            'Harga Basis',
            'Harga Basis Pembelian',
            'Harga Netto',
            'Created At',
            'Updated At',
        ];
    }

    public function map($pembelianDetail): array
    {
        return [
            $pembelianDetail->id,
            $pembelianDetail->pembelian->no_transaksi,
            $pembelianDetail->produk->nama_produk,
            $pembelianDetail->netto,
            $pembelianDetail->satuan,
            $pembelianDetail->rendeman,
            $pembelianDetail->bobot,
            $pembelianDetail->harga,
            $pembelianDetail->harga_basis,
            $pembelianDetail->harga_basis_pembelian,
            $pembelianDetail->harga_netto,
            $pembelianDetail->created_at->format('d-m-Y H:i:s'),
            $pembelianDetail->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
