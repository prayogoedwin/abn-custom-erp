<?php

namespace App\Exports;

use App\Models\Pihak3;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Pihak3Export implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pihak3::get();
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

    public function map($pihak3): array
    {
        return [
            $pihak3->id,
            $pihak3->user_id,
            $pihak3->nama,
            $pihak3->kontak,
            $pihak3->alamat,
            $pihak3->isactive ? 'Active' : 'Inactive',
            $pihak3->created_at->format('d-m-Y H:i:s'),
            $pihak3->created_by,
            $pihak3->updated_at?->format('d-m-Y H:i:s'),
            $pihak3->updated_by,
            $pihak3->deleted_at?->format('d-m-Y H:i:s'),
            $pihak3->deleted_by,
        ];
    }
}
