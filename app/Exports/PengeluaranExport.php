<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengeluaranExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pengeluaran::with('kategori_pengeluaran')->where('deleted_at', null)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pengeluaran',
            'Kategori Pengeluaran',
            'Tanggal',
            'Created At',
            'Updated At',
        ];
    }

    public function map($pengeluaran): array
    {
        return [
            $pengeluaran->id,
            $pengeluaran->nama_pengeluaran,
            $pengeluaran->kategori_pengeluaran?->nama ?? '-',
            $pengeluaran->tanggal->format('d-m-Y'),
            $pengeluaran->created_at->format('d-m-Y H:i:s'),
            $pengeluaran->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
