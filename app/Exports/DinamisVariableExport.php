<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\DinamisVariable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DinamisVariableExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DinamisVariable::where('is_active', true)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Jenis Variable',
            'Keterangan',
            'Nilai',
        ];
    }

    public function map($dinamisVariable): array
    {
        return [
            $dinamisVariable->id,
            $dinamisVariable->name,
            $dinamisVariable->jenis,
            $dinamisVariable->keterangan,
            $dinamisVariable->variable_value,
        ];
    }
}
