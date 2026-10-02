<?php

namespace App\Exports\Laporan;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiayaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Collection $rows;
    protected string $startdate;
    protected string $enddate;
    protected float $totalPengeluaran;
    protected float $totalPembelian;
    protected float $totalBiaya;
    protected int $rowCount;

    public function __construct(
        Collection $rows,
        string $startdate,
        string $enddate,
        float $totalPengeluaran,
        float $totalPembelian
    ) {
        $this->rows             = $rows;
        $this->startdate        = $startdate;
        $this->enddate          = $enddate;
        $this->totalPengeluaran = $totalPengeluaran;
        $this->totalPembelian   = $totalPembelian;
        $this->totalBiaya       = $totalPengeluaran + $totalPembelian;
        $this->rowCount         = $rows->count();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            ['Periode Laporan:', $this->startdate . ' s/d ' . $this->enddate],
            ['Total Pengeluaran:', $this->totalPengeluaran, 'Total Pembelian:', $this->totalPembelian],
            ['Total Biaya:', $this->totalBiaya],
            [],
            ['Transaksi', 'Nominal', 'Sumber', 'Tanggal'],
        ];
    }

    public function map($row): array
    {
        return [
            $row['transaksi'],
            $row['nominal'],
            $row['sumber'],
            $row['tanggal'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $startDataRow = 6;
        $endDataRow   = $this->rowCount === 0
            ? $startDataRow
            : $startDataRow + $this->rowCount - 1;
        $totalRow = $endDataRow + 1;

        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->setCellValue("B{$totalRow}", "=SUM(B{$startDataRow}:B{$endDataRow})");

        $currencyFormat = '#,##0';
        $sheet->getStyle("B{$startDataRow}:B{$totalRow}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('B2:B3')->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('D2:D3')->getNumberFormat()->setFormatCode($currencyFormat);

        // Rata kiri untuk nilai ringkasan di header
        $sheet->getStyle('B1:D3')->getAlignment()->setHorizontal('left');

        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
            $totalRow => [
                'font'    => ['bold' => true],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                ],
            ],
        ];
    }
}