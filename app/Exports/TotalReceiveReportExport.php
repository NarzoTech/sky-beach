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

class TotalReceiveReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $totalReceive;
    private $data;

    public function __construct($totalReceive, $data = null)
    {
        $this->totalReceive = $totalReceive;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->totalReceive;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Payment Received Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Invoice Type'),
                __('Invoice'),
                __('Customer'),
                __('Total Amount'),
                __('Pay By'),
            ],
        ];
    }
    public function map($receive): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            date('d-m-Y', strtotime($receive->payment_date)),
            ucwords($receive->payment_type),
            $receive->invoice ?? $receive->sale->invoice ?? '',
            $receive->customer->name ?? 'Guest',
            $receive->amount,
            $receive->account->account_type ?? '',
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
                $event->sheet->setCellValue('F' . $lastRow, currency($data['receive'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, '');
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
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(15);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Payment Received Report';
    }
}
