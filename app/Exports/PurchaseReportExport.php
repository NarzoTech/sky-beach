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

class PurchaseReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $purchases;
    private $data;

    public function __construct($purchases, $data = null)
    {
        $this->purchases = $purchases;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->purchases;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Purchase Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Invoice'),
                __('Supplier'),
                __('Purchased By'),
                __('Invoice Qty'),
                __('Total'),
                __('Paid'),
                __('Due'),
                __('Payment Status'),
            ],
        ];
    }
    public function map($purchase): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            now()->parse($purchase->purchase_date)->format('d-m-Y'),
            $purchase->invoice_number,
            $purchase->supplier->name ?? 'Guest',
            $purchase->createdBy->name ?? '',
            $purchase->purchaseDetails->sum('quantity'),
            (int) $purchase->total_amount,
            (int) $purchase->paid_amount,
            (int) $purchase->due_amount,
            $purchase->due_amount == 0 ? 'Paid' : 'Due',
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
                $event->sheet->setCellValue('E' . $lastRow, '');
                $event->sheet->setCellValue('F' . $lastRow, 'Total');
                $event->sheet->setCellValue('G' . $lastRow, currency($data['total_amount'] ?? 0));
                $event->sheet->setCellValue('H' . $lastRow, currency($data['paid_amount'] ?? 0));
                $event->sheet->setCellValue('I' . $lastRow, currency($data['due_amount'] ?? 0));
                $event->sheet->setCellValue('J' . $lastRow, '');
                $event->sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');  // Title
        $sheet->mergeCells('A2:J2');  // Subtitle
        $sheet->mergeCells('A3:J3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Purchase Report';
    }
}
