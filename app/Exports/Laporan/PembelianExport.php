<?php

namespace App\Exports\Laporan;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PembelianExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'No Transaksi',
            'Supplier',
            'Details',
            'Total',
            'Kekurangan',
            'Status',
            'Tanggal',

        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->no_transaksi,
            $data->supplier,
            $data->produk_list,
            $data->total_nominal_pembelian,
            $data->kekurangan,
            $data->status_pembayaran,
            $data->created_at,
        ];
    }
}
