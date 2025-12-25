<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $sales;
    private $totals;

    public function __construct($sales)
    {
        $this->sales = $sales;

        // Calculate totals
        $this->totals = [
            'total_price' => 0,
            'grand_total' => 0,
            'paid_amount' => 0,
            'due_amount' => 0,
        ];

        foreach ($sales as $sale) {
            $this->totals['total_price'] += (float)$sale->total_price;
            $this->totals['grand_total'] += (float)$sale->grand_total;
            $this->totals['paid_amount'] += (float)$sale->paid_amount;
            $this->totals['due_amount'] += (float)$sale->due_amount;
        }
    }

    public function array(): array
    {
        $data = [];
        $count = 0;

        foreach ($this->sales as $sale) {
            // Payment status logic
            if ((float)$sale->paid_amount >= (float)$sale->grand_total) {
                $paymentStatus = __('Paid');
            } elseif ((float)$sale->paid_amount == 0) {
                $paymentStatus = __('Due');
            } else {
                $paymentStatus = __('Partial Due');
            }

            $data[] = [
                ++$count,
                $sale->order_date ? $sale->order_date->format('d-m-y') : '',
                $sale->invoice,
                $sale?->customer?->name ?? 'Guest',
                $sale->sale_note,
                $sale->total_price,
                $sale->grand_total,
                $sale->paid_amount,
                $sale->due_amount,
                $paymentStatus
            ];
        }

        // Add totals row
        $data[] = [
            '',
            '',
            '',
            '',
            __('Total'),
            $this->totals['total_price'],
            $this->totals['grand_total'],
            $this->totals['paid_amount'],
            $this->totals['due_amount'],
            ''
        ];

        return $data;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Sales List'],
            ['Time: ' . now()],
            [
                __('SL.'),
                __('Date'),
                __('Invoice No'),
                __('Customer'),
                __('Remark'),
                __('Sale Amount'),
                __('Total Amount'),
                __('Paid Amount'),
                __('Due'),
                __('Payment Status')
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15); // Increased width for Payment Status

        // Center align title rows
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Bold the header row
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);

        // Bold the totals row (last row)
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
    }

    public function title(): string
    {
        return 'Sales List';
    }
}
