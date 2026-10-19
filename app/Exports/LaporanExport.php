<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    protected Collection $rows;
    protected array $headings;
    protected array $headerInfo;
    protected array $excludeTotalColumns;

    /** [index kolom (0-based) => total] */
    protected array $totals = [];

    /**
     * @param array $excludeTotalColumns Nama heading yang TIDAK ingin ditotal (mis. ['Harga'])
     */
    public function __construct(
        Collection $rows,
        array $headings,
        array $headerInfo = [],
        array $excludeTotalColumns = []
    ) {
        $this->rows = $rows;
        $this->headings = $headings;
        $this->headerInfo = $headerInfo;
        $this->excludeTotalColumns = $excludeTotalColumns;

        $this->totals = $this->hitungTotal();
    }

    /**
     * Hitung total tiap kolom yang bertipe int/float.
     * String numerik (mis. nomor telepon / no transaksi) sengaja TIDAK dihitung.
     */
    protected function hitungTotal(): array
    {
        $totals = [];

        foreach ($this->rows as $row) {
            foreach (array_values((array) $row) as $i => $value) {
                if (in_array($this->headings[$i] ?? null, $this->excludeTotalColumns, true)) {
                    continue;
                }

                if (is_int($value) || is_float($value)) {
                    $totals[$i] = ($totals[$i] ?? 0) + $value;
                }
            }
        }

        return $totals;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function map($row): array
    {
        return array_values((array) $row);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function startCell(): string
    {
        return 'A' . (count($this->headerInfo) + 2);
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = count($this->headerInfo) + 2;

        return [
            $headerRow => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '198754'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $infoCount = count($this->headerInfo);
                $headerRow = $infoCount + 2;
                $lastRow = $sheet->getHighestRow();      // baris data terakhir (sebelum total)
                $lastColumn = $sheet->getHighestColumn();
                $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);

                // Informasi header laporan
                $row = 1;

                foreach ($this->headerInfo as $label => $value) {
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", $value);

                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);

                    $row++;
                }

                // Jarak antara informasi laporan dan tabel
                $sheet->getRowDimension($infoCount + 1)->setRowHeight(8);
                $sheet->getRowDimension($headerRow)->setRowHeight(25);

                // Perataan vertikal
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$lastRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP);

                // Warna selang-seling
                for ($r = $headerRow + 1; $r <= $lastRow; $r++) {
                    if (($r - $headerRow) % 2 === 1) {
                        $sheet->getStyle("A{$r}:{$lastColumn}{$r}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID);

                        $sheet->getStyle("A{$r}:{$lastColumn}{$r}")
                            ->getFill()
                            ->getStartColor()
                            ->setRGB('F2F7F4');
                    }
                }

                // Filter hanya pada data (baris total tidak ikut ter-filter/sort)
                $sheet->setAutoFilter(
                    "A{$headerRow}:{$lastColumn}{$lastRow}"
                );

                $tableLastRow = $lastRow;

                // Baris total (jumlah kolom numerik)
                if ($this->rows->isNotEmpty() && !empty($this->totals)) {
                    $totalRow = $tableLastRow + 1;
                    $tableLastRow = $totalRow;

                    // Label "Total" di kolom pertama yang bukan kolom total
                    $totalCols = array_keys($this->totals);
                    foreach (array_keys($this->headings) as $i) {
                        if (!in_array($i, $totalCols, true)) {
                            $sheet->setCellValue(
                                Coordinate::stringFromColumnIndex($i + 1) . $totalRow,
                                'Total'
                            );
                            break;
                        }
                    }

                    // Nilai total per kolom (angka biasa, bukan formula)
                    foreach ($this->totals as $i => $total) {
                        $cell = Coordinate::stringFromColumnIndex($i + 1) . $totalRow;
                        $total = round($total, 2);

                        $sheet->setCellValue($cell, $total);
                        $sheet->getStyle($cell)
                            ->getNumberFormat()
                            ->setFormatCode(floor($total) == $total ? '#,##0' : '#,##0.00');
                        $sheet->getStyle($cell)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }

                    $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D1E7DD'],
                        ],
                    ]);
                    $sheet->getRowDimension($totalRow)->setRowHeight(22);
                    $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Baris total data (jumlah baris), paling bawah
                $countRow = $tableLastRow + 1;
                $tableLastRow = $countRow;
                $jumlahData = $this->rows->count();

                if ($lastColumnIndex >= 2) {
                    $sheet->setCellValue("A{$countRow}", 'Total Data');
                    $sheet->setCellValue("B{$countRow}", $jumlahData);
                    $sheet->getStyle("B{$countRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                    $sheet->getStyle("B{$countRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } else {
                    // Hanya 1 kolom: gabung label dan jumlah dalam satu sel
                    $sheet->setCellValue("A{$countRow}", 'Total Data: ' . number_format($jumlahData, 0, ',', '.'));
                }

                $sheet->getStyle("A{$countRow}:{$lastColumn}{$countRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9F5EE'],
                    ],
                ]);
                $sheet->getRowDimension($countRow)->setRowHeight(22);
                $sheet->getStyle("A{$countRow}:{$lastColumn}{$countRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Border tabel (termasuk baris total dan total data)
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$tableLastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB('D9E2F3');
            },
        ];
    }
}
