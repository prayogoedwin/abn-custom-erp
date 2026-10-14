<?php

namespace App\Exports;

use App\Models\BonPihak3;
use App\Models\Pihak3;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BonPihak3Export implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    protected $pihak3;
    protected $startdate;
    protected $enddate;
    protected $namaPihak3;

    public function __construct($pihak3, $startdate = null, $enddate = null)
    {
        $this->pihak3 = $pihak3;
        $this->startdate = $startdate;
        $this->enddate = $enddate;

        $this->namaPihak3 = Pihak3::find($pihak3)?->nama;
    }

    public function query()
    {
        $query = BonPihak3::query()
            ->where('pihak3_id', $this->pihak3)
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
            ['LAPORAN BON PIHAK 3'],
            ['Nama Pihak 3: ' . $this->namaPihak3],
            [
                'Tanggal',
                'Keterangan',
                'Masuk',
                'Keluar',
                'Sisa',
            ],
        ];
    }

    public function map($bonPihak3): array
    {
        return [
            $bonPihak3->created_at
                ? $bonPihak3->created_at->translatedFormat('d M Y')
                : '',
            $bonPihak3->keterangan,
            $bonPihak3->masuk,
            $bonPihak3->keluar,
            $bonPihak3->generateSisa(),
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
