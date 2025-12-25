<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $index;
    private $totalAmount = 0;

    public function __construct(private $expenses)
    {
        $this->totalAmount = $expenses->sum('amount');
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
            [$setting->app_name],  // Title rows
            ['Expense Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Created By'),
                __('Type'),
                __('Note'),
                __('Amount')
            ],
        ];
    }
    public function map($expense): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            now()->parse($expense->date)->format('d-m-Y'),
            $expense->createdBy->name,
            $expense->expenseType->name,
            $expense->note,
            $expense->amount,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:F1');  // Title
        $sheet->mergeCells('A2:F2');  // Subtitle
        $sheet->mergeCells('A3:F3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:F' . $sheet->getHighestRow())
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



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add total row
        $highestRow = $sheet->getHighestRow();
        $totalRow = $highestRow + 1;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, '');
        $sheet->setCellValue('C' . $totalRow, '');
        $sheet->setCellValue('D' . $totalRow, '');
        $sheet->setCellValue('E' . $totalRow, __('Total'));
        $sheet->setCellValue('F' . $totalRow, $this->totalAmount);

        // Style the total row
        $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }

    public function title(): string
    {
        return 'Expense Report';
    }
}
