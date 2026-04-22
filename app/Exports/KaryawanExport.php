<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Karyawan::get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'user_id',
            'nama',
            'email',
            'Nomor Pegawai',
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

    public function map($karyawan): array
    {
        return [
            $karyawan->id,
            $karyawan->user_id,
            $karyawan->nama,
            $karyawan->user->email,
            $karyawan->noPegawai,
            $karyawan->kontak,
            $karyawan->alamat,
            $karyawan->isactive ? 'Active' : 'Inactive',
            $karyawan->created_at->format('d-m-Y H:i:s'),
            $karyawan->created_by,
            $karyawan->updated_at?->format('d-m-Y H:i:s'),
            $karyawan->updated_by,
            $karyawan->deleted_at?->format('d-m-Y H:i:s'),
            $karyawan->deleted_by,
        ];
    }
}
