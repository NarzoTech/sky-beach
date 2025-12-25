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

class BarcodeWiseProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $products;
    private $data;

    public function __construct($products, $data = null)
    {
        $this->products = $products;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->products;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Barcode Wise Product Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Product Name'),
                __('Attribute'),
                __('Barcode'),
                __('Brand Name'),
                __('Sale'),
                __('Sale Return'),
                __('Purchase'),
            ],
        ];
    }
    public function map($product): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $product->name,
            $product->attribute,
            $product->barcode,
            $product->brand->name ?? 'N/A',
            currency((int) $product->sales['price']) . "({$product->sales['qty']})",
            currency((int) $product->sales_return['price']) . "({$product->sales_return['qty']})",
            currency((int) $product->total_purchase['price']) . "({$product->total_purchase['qty']})"
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, '');
                $event->sheet->setCellValue('C' . $lastRow, '');
                $event->sheet->setCellValue('D' . $lastRow, '');
                $event->sheet->setCellValue('E' . $lastRow, 'Total');
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalSalePrice'] ?? 0) . "({$data['totalSaleQty'] ?? 0})");
                $event->sheet->setCellValue('G' . $lastRow, currency($data['totalReturnPrice'] ?? 0) . "({$data['totalReturnQty'] ?? 0})");
                $event->sheet->setCellValue('H' . $lastRow, currency($data['totalPurchasePrice'] ?? 0) . "({$data['totalPurchaseQty'] ?? 0})");
                $event->sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:H1');  // Title
        $sheet->mergeCells('A2:H2');  // Subtitle
        $sheet->mergeCells('A3:H3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:H' . $sheet->getHighestRow())
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


        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Barcode Wise Product Report';
    }
}
