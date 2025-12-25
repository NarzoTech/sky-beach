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

class SuppliersPaymentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $supplierPayments;
    private $data;

    public function __construct($supplierPayments, $data = null)
    {
        $this->supplierPayments = $supplierPayments;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->supplierPayments;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier Payment Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Invoice'),
                __('Suppliers'),
                __('Total'),
                __('Paid'),
                __('Due'),
                __('Return')
            ],
        ];
    }
    public function map($supplierPayment): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            formatDate($supplierPayment->purchase_date),
            $supplierPayment->invoice_number,
            $supplierPayment->supplier->name ?? '',
            currency($supplierPayment->total_amount),
            currency($supplierPayment->paid_amount),
            currency($supplierPayment->due_amount - $supplierPayment->purchaseReturn->sum('return_amount') + $supplierPayment->purchaseReturn->sum('received_amount')),
            currency($supplierPayment->purchaseReturn->sum('return_amount')),
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
                $event->sheet->setCellValue('E' . $lastRow, currency($data['total'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['paid_amount'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, currency($data['due_amount'] ?? 0));
                $event->sheet->setCellValue('H' . $lastRow, currency($data['return_amount'] ?? 0));
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
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);



        // a1 to h1 will be center aligned
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to h2, a3 to h3  will be center aligned
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Supplier Payment Report';
    }
}
