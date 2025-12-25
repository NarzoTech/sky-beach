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

class SuppliersReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $suppliers;
    private $data;

    public function __construct($suppliers, $data = null)
    {
        $this->suppliers = $suppliers;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->suppliers;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Name'),
                __('Company'),
                __('Phone'),
                __('Total Purchase'),
                __('Total'),
                __('Paid'),
                __('Due'),
            ],
        ];
    }
    public function map($supplier): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $supplier->name,
            $supplier->company,
            $supplier->phone,
            $supplier->purchases->count(),
            currency($supplier->purchases->sum('total_amount')),
            currency($supplier->total_paid ?? 0),
            currency($supplier->total_due),
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
                $event->sheet->setCellValue('D' . $lastRow, 'Total');
                $event->sheet->setCellValue('E' . $lastRow, $data['purchase_count'] ?? 0);
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalPurchase'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, currency($data['pay'] ?? 0));
                $event->sheet->setCellValue('H' . $lastRow, currency($data['total_due'] ?? 0));
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
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Supplier Report';
    }
}
