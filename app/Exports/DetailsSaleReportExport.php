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

class DetailsSaleReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $sales;
    private $data;

    public function __construct($sales, $data = null)
    {
        $this->sales = $sales;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->sales;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Details Sales Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Invoice'),
                __('Customer'),
                __('Total '),
                __('Paid'),
                __('Paid By'),
                __('Due'),
                __('Return Amount'),
                __('Payment Status'),
            ],
        ];
    }
    public function map($sale): array
    {
        $paymentMethods = '';

        foreach ($sale->payment as $payment) {
            if ($payment->amount == 0) continue;
            $paymentMethods .= $payment->account->account_type . ':' . $payment->amount . ', ';
        }
        $paymentMethods = rtrim($paymentMethods, ', ');

        // Map the data to match your format
        return [
            ++$this->index,
            $sale->order_date->format('d-m-Y'),
            $sale->invoice,
            $sale?->customer?->name ?? 'Guest',
            $sale->grand_total,
            $sale->paid_amount,
            $paymentMethods,
            $sale->due_amount,
            0,
            $sale->due_amount == 0 ? 'Paid' : 'Due',
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
                $event->sheet->setCellValue('E' . $lastRow, currency($data['total_amount'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['paid_amount'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, '');
                $event->sheet->setCellValue('H' . $lastRow, currency($data['due_amount'] ?? 0));
                $event->sheet->setCellValue('I' . $lastRow, currency($data['return_amount'] ?? 0));
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
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Details Sales Report';
    }
}
