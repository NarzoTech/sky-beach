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


class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index = 0;
    private $totalAmount = 0;
    private $totalPaid = 0;
    private $totalDue = 0;

    public function __construct(private $expenses)
    {
        $this->totalAmount = $expenses->sum('amount');
        $this->totalPaid = $expenses->sum('paid_amount');
        $this->totalDue = $expenses->sum('due_amount');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Expenses Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Invoice'),
                __('Date'),
                __('Supplier'),
                __('Type'),
                __('Amount'),
                __('Paid'),
                __('Due'),
                __('Status'),
                __('Note'),
                __('Memo'),
            ],
        ];
    }

    public function map($expense): array
    {
        $status = $expense->payment_status_label;
        $statusLabel = $status == 'paid' ? __('Paid') : ($status == 'partial' ? __('Partial') : __('Due'));

        return [
            ++$this->index,
            $expense->invoice ?? '-',
            $expense->date,
            $expense->expenseSupplier->name ?? '-',
            $expense->expenseType->name,
            $expense->amount,
            $expense->paid_amount,
            $expense->due_amount,
            $statusLabel,
            $expense->note,
            $expense->memo,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'K';

        // Merge cells for title and subtitle
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle("A4:{$lastCol}" . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);   // SN
        $sheet->getColumnDimension('B')->setWidth(15);  // Invoice
        $sheet->getColumnDimension('C')->setWidth(15);  // Date
        $sheet->getColumnDimension('D')->setWidth(20);  // Supplier
        $sheet->getColumnDimension('E')->setWidth(20);  // Type
        $sheet->getColumnDimension('F')->setWidth(12);  // Amount
        $sheet->getColumnDimension('G')->setWidth(12);  // Paid
        $sheet->getColumnDimension('H')->setWidth(12);  // Due
        $sheet->getColumnDimension('I')->setWidth(12);  // Status
        $sheet->getColumnDimension('J')->setWidth(25);  // Note
        $sheet->getColumnDimension('K')->setWidth(25);  // Memo

        // Center align title rows
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A2:{$lastCol}2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A3:{$lastCol}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Bold the header row
        $sheet->getStyle("A4:{$lastCol}4")->getFont()->setBold(true);
    }

    public function title(): string
    {
        return 'Expenses Report';
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
                $sheet->setCellValue('D' . $totalRow, '');
                $sheet->setCellValue('E' . $totalRow, __('Total'));
                $sheet->setCellValue('F' . $totalRow, $this->totalAmount);
                $sheet->setCellValue('G' . $totalRow, $this->totalPaid);
                $sheet->setCellValue('H' . $totalRow, $this->totalDue);
                $sheet->setCellValue('I' . $totalRow, '');
                $sheet->setCellValue('J' . $totalRow, '');
                $sheet->setCellValue('K' . $totalRow, '');

                // Style the total row
                $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
