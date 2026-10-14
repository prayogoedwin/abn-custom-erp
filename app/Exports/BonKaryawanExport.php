<?php

namespace App\Exports;

use App\Models\BonKaryawan;
use App\Models\Karyawan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BonKaryawanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    protected $karyawan;
    protected $startdate;
    protected $enddate;
    protected $namaKaryawan;

    public function __construct($karyawan, $startdate = null, $enddate = null)
    {
        $this->karyawan = $karyawan;
        $this->startdate = $startdate;
        $this->enddate = $enddate;

        $this->namaKaryawan = Karyawan::find($karyawan)?->nama;
    }

    public function query()
    {
        $query = BonKaryawan::query()
            ->where('karyawan_id', $this->karyawan)
            ->orderBy('id');

        if ($this->startdate && $this->enddate) {
            $start = Carbon::parse($this->startdate)->startOfDay();
            $end = Carbon::parse($this->enddate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN BON KARYAWAN'],
            ['Nama Karyawan: ' . $this->namaKaryawan],
            [
                'Tanggal',
                'Keterangan',
                'Masuk',
                'Keluar',
                'Sisa',
            ],
        ];
    }

    public function map($bonKaryawan): array
    {
        return [
            $bonKaryawan->created_at
                ? $bonKaryawan->created_at->translatedFormat('d M Y')
                : '',
            $bonKaryawan->keterangan,
            $bonKaryawan->masuk,
            $bonKaryawan->keluar,
            $bonKaryawan->generateSisa(),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
            ],
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '198754'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Judul laporan
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');

                // Tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(22);

                // Format angka
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("C4:E{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00;[Red]-#,##0.00');

                // Border tabel
                $sheet->getStyle("A3:E{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    );
            },
        ];
    }
}
