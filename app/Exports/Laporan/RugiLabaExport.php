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

class RugiLabaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Collection $rows;
    protected string $startdate;
    protected string $enddate;
    protected float $totalPemasukan;
    protected float $totalPengeluaran; // pembelian + pengeluaran
    protected float $labaRugi;
    protected int $rowCount;

    public function __construct(
        Collection $rows,
        string $startdate,
        string $enddate,
        float $totalPemasukan,
        float $totalPengeluaran
    ) {
        $this->rows             = $rows;
        $this->startdate        = $startdate;
        $this->enddate          = $enddate;
        $this->totalPemasukan   = $totalPemasukan;
        $this->totalPengeluaran = $totalPengeluaran;
        $this->labaRugi         = $totalPemasukan - $totalPengeluaran;
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
            ['Total Pemasukan:', $this->totalPemasukan, 'Total Pengeluaran:', $this->totalPengeluaran],
            [$this->labaRugi >= 0 ? 'Laba Bersih:' : 'Rugi Bersih:', $this->labaRugi],
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

        // Baris total di bawah data (dihitung per sumber, bukan SUM semua
        // karena penjualan = pemasukan sedangkan lainnya = pengeluaran)
        $rowPemasukan   = $endDataRow + 1;
        $rowPengeluaran = $endDataRow + 2;
        $rowLabaRugi    = $endDataRow + 3;

        $rangeB = "B{$startDataRow}:B{$endDataRow}";
        $rangeC = "C{$startDataRow}:C{$endDataRow}";

        $sheet->setCellValue("A{$rowPemasukan}", 'TOTAL PEMASUKAN (Penjualan)');
        $sheet->setCellValue("B{$rowPemasukan}", "=SUMIF({$rangeC},\"Penjualan\",{$rangeB})");

        $sheet->setCellValue("A{$rowPengeluaran}", 'TOTAL PENGELUARAN (Pembelian + Pengeluaran)');
        $sheet->setCellValue(
            "B{$rowPengeluaran}",
            "=SUMIF({$rangeC},\"Pembelian\",{$rangeB})+SUMIF({$rangeC},\"Pengeluaran\",{$rangeB})"
        );

        $sheet->setCellValue("A{$rowLabaRugi}", 'LABA / RUGI');
        $sheet->setCellValue("B{$rowLabaRugi}", "=B{$rowPemasukan}-B{$rowPengeluaran}");

        $currencyFormat = '#,##0';
        $sheet->getStyle("B{$startDataRow}:B{$rowLabaRugi}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('B2:B3')->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('D2:D3')->getNumberFormat()->setFormatCode($currencyFormat);
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
            $rowPemasukan   => [
                'font'    => ['bold' => true],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ],
            $rowPengeluaran => ['font' => ['bold' => true]],
            $rowLabaRugi    => [
                'font'    => ['bold' => true],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                ],
            ],
        ];
    }
}