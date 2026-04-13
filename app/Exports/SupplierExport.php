<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Supplier::get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'user_id',
            'nama',
            'kontak',
            'alamat',
            'Status',
            'Created At',
            'Created By',
            'Updated At',
            'Updated By',
            'Deleted At',
            'Deleted By',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->user_id,
            $supplier->nama,
            $supplier->kontak,
            $supplier->alamat,
            $supplier->isactive ? 'Active' : 'Inactive',
            $supplier->created_at->format('Y-m-d H:i:s'),
            $supplier->created_by,
            $supplier->updated_at?->format('Y-m-d H:i:s'),
            $supplier->updated_by,
            $supplier->deleted_at?->format('Y-m-d H:i:s'),
            $supplier->deleted_by,
        ];
    }
}
