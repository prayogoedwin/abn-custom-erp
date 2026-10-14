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

    public function __construct(
        Collection $rows,
        array $headings,
        array $headerInfo = []
    ) {
        $this->rows = $rows;
        $this->headings = $headings;
        $this->headerInfo = $headerInfo;
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
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

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

                // Border tabel
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB('D9E2F3');

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

                // Filter hanya pada heading tabel
                $sheet->setAutoFilter(
                    "A{$headerRow}:{$lastColumn}{$lastRow}"
                );

                // Bekukan heading tabel
                // $sheet->freezePane('A' . ($headerRow + 1));
            },
        ];
    }
}
