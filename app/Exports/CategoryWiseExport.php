<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryWiseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $categories;
    private $data;

    public function __construct($categories, $data = null)
    {
        $this->categories = $categories;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->categories;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Category Wise Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Category Name'),
                __('Purchase'),
                __('Sold'),
                __('Purchase Amount'),
                __('Sold Amount')
            ],
        ];
    }
    public function map($category): array
    {

        // Map the data to match your format
        return [
            ++$this->index,
            $category->name,
            $category->PurchaseSummary['count'],
            $category->sales_count,
            currency($category->PurchaseSummary['amount']),
            currency($category->sales_amount),
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, 'Total');
                $event->sheet->setCellValue('C' . $lastRow, $data['totalPurchaseCount'] ?? 0);
                $event->sheet->setCellValue('D' . $lastRow, $data['totalSalesCount'] ?? 0);
                $event->sheet->setCellValue('E' . $lastRow, currency($data['totalPurchaseAmount'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalSalesAmount'] ?? 0));
                $event->sheet->getStyle('A' . $lastRow . ':F' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:F1');  // Title
        $sheet->mergeCells('A2:F2');  // Subtitle
        $sheet->mergeCells('A3:F3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:F' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);


        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Category Wise Report';
    }
}
