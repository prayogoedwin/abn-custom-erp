<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Absensi::with('karyawan')->where('is_active', true)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Bulan',
            'Tahun',
            'Jumlah Hari Hadir',
            'Jumlah Hari Izin',
            'Jumlah Hari Alpha',
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->id,
            $absensi->karyawan->nama,
            $absensi->bulan,
            $absensi->tahun,
            $absensi->jumlah_masuk,
            $absensi->jumlah_izin,
            $absensi->jumlah_absen,
        ];
    }
}
