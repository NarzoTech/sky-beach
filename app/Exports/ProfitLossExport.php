<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            // Income Section
            [__('INCOME'), ''],
            [__('Total Sales') . ' (' . __('incl. Tax') . ')', currency($this->data['totalSales'])],
            [__('Less: Tax Collected') . ' (' . __('Govt. Liability') . ')', '- ' . currency($this->data['totalTax'])],
            [__('Net Sales') . ' (' . __('excl. Tax') . ')', currency($this->data['netSales'])],
            [__('Purchase Returns') . ' (' . __('Refund from Supplier') . ')', currency($this->data['purchaseReturns'])],
            [__('Total Income'), currency($this->data['totalIncome'])],
            ['', ''],
            // Expense Section
            [__('EXPENSES'), ''],
            [__('Cost of Goods Sold (COGS)'), currency($this->data['cogs'])],
            [__('Gross Profit') . ' (' . __('Net Sales - COGS') . ')', currency($this->data['grossProfit'])],
            [__('Operating Expenses'), currency($this->data['expenses'])],
            [__('Employee Salaries'), currency($this->data['salaries'])],
            [__('Total Expenses'), currency($this->data['totalExpenses'])],
            ['', ''],
            // Profit/Loss
            [__('NET PROFIT / LOSS'), currency($this->data['profitLoss'])],
        ];
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            [__('Profit/Loss Report')],
            [__('Period') . ': ' . $this->data['fromDate'] . ' - ' . $this->data['toDate']],
            ['Time: ' . now()->format('d-m-Y H:i:s')],
            [__('Description'), __('Amount')],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Merge cells for title rows
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');

        // Apply styles to title rows
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(10);

        // Apply borders to data rows
        $sheet->getStyle('A5:B' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Center align title rows
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Right align amount column
        $sheet->getStyle('B6:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Style section headers (Income - row 6)
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A6:B6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');

        // Style Tax Collected row (row 8) - red text
        $sheet->getStyle('A8:B8')->getFont()->getColor()->setRGB('DC3545');

        // Style Net Sales row (row 9)
        $sheet->getStyle('A9')->getFont()->setBold(true);
        $sheet->getStyle('A9:B9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');

        // Style Total Income row (row 11)
        $sheet->getStyle('A11')->getFont()->setBold(true);
        $sheet->getStyle('A11:B11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C3E6CB');

        // Style Expenses header (row 13)
        $sheet->getStyle('A13')->getFont()->setBold(true);
        $sheet->getStyle('A13:B13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');

        // Style Gross Profit row (row 15)
        $sheet->getStyle('A15')->getFont()->setBold(true);
        $sheet->getStyle('A15:B15')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');

        // Style Total Expenses row (row 18)
        $sheet->getStyle('A18')->getFont()->setBold(true);
        $sheet->getStyle('A18:B18')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5C6CB');

        // Style Net Profit/Loss row (row 20)
        $sheet->getStyle('A20')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B20')->getFont()->setBold(true)->setSize(12);

        if ($this->data['profitLoss'] >= 0) {
            $sheet->getStyle('A20:B20')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
            $sheet->getStyle('A20:B20')->getFont()->getColor()->setRGB('FFFFFF');
        } else {
            $sheet->getStyle('A20:B20')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DC3545');
            $sheet->getStyle('A20:B20')->getFont()->getColor()->setRGB('FFFFFF');
        }
    }

    public function title(): string
    {
        return __('Profit Loss Report');
    }
}
