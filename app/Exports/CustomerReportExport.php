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

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $customers;
    private $data;

    public function __construct($customers, $data = null)
    {
        $this->customers = $customers;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->customers;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Customer Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Name'),
                __('Phone'),
                __('Total Sales'),
                __('Total'),
                __('Paid'),
                __('Due')
            ],
        ];
    }
    public function map($customer): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $customer->name,
            $customer->phone,
            $customer->sales->count(),
            currency($customer->sales->sum('grand_total')),
            currency($customer->total_paid),
            currency($customer->total_due),
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
                $event->sheet->setCellValue('C' . $lastRow, 'Total');
                $event->sheet->setCellValue('D' . $lastRow, $data['totalSales'] ?? 0);
                $event->sheet->setCellValue('E' . $lastRow, currency($data['totalAmount'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalPaid'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, currency($data['totalDue'] ?? 0));
                $event->sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:G1');  // Title
        $sheet->mergeCells('A2:G2');  // Subtitle
        $sheet->mergeCells('A3:G3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);


        // a1 to g1 will be center aligned
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to g2, a3 to g3  will be center aligned
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Customer Report';
    }
}
