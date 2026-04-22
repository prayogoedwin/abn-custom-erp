<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::get();
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

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->user_id,
            $customer->nama,
            $customer->kontak,
            $customer->alamat,
            $customer->isactive ? 'Active' : 'Inactive',
            $customer->created_at->format('d-m-Y H:i:s'),
            $customer->created_by,
            $customer->updated_at?->format('d-m-Y H:i:s'),
            $customer->updated_by,
            $customer->deleted_at?->format('d-m-Y H:i:s'),
            $customer->deleted_by,
        ];
    }
}
