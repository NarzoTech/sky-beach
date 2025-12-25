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


class PurchaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $count;
    private $totalAmount = 0;
    private $totalPaid = 0;
    private $totalDue = 0;

    public function __construct(private $purchases) {}
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
            ['Purchase List'],
            ['Time: ' . now()],
            [
                'SL',
                __('Date'),
                __('Invoice Number'),
                __('Supplier'),
                __('Total Amount'),
                __('Total Pay'),
                __('Total Due')
            ],
        ];
    }

    public function map($purchase): array
    {
        // Track totals
        $this->totalAmount += $purchase->total_amount;
        $this->totalPaid += $purchase->paid_amount;
        $this->totalDue += $purchase->due_amount;

        // Map the data to match your format
        return [
            ++$this->count,
            $purchase->purchase_date,
            $purchase->invoice_number,
            $purchase->supplier?->name,
            $purchase->total_amount,
            $purchase->paid_amount,
            $purchase->due_amount,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:G1');  // Title
        $sheet->mergeCells('A2:G2');  // Subtitle
        $sheet->mergeCells('A3:G3');  // Time

        // Merge cells for top-level headers
        // $sheet->mergeCells('E4:G4');  // Purchase spans 'Total' and 'Pay'
        // $sheet->mergeCells('H4:J4');  // Purchase Return spans 'Total' and 'Pay'

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows

        $sheet->getStyle('A5:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);


        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }


    public function title(): string
    {
        return 'Purchase List';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $totalRow = $highestRow + 1;

                // Add total row
                $sheet->setCellValue('A' . $totalRow, '');
                $sheet->setCellValue('B' . $totalRow, '');
                $sheet->setCellValue('C' . $totalRow, '');
                $sheet->setCellValue('D' . $totalRow, __('Total'));
                $sheet->setCellValue('E' . $totalRow, $this->totalAmount);
                $sheet->setCellValue('F' . $totalRow, $this->totalPaid);
                $sheet->setCellValue('G' . $totalRow, $this->totalDue);

                // Style the total row
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
