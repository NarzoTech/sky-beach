<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $users;
    private $data;

    public function __construct($users, $data = [])
    {
        $this->users = $users;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        // Add data rows with serial numbers
        foreach ($this->users as $index => $user) {
            $rows[] = [
                $index + 1,                                    // SL (Serial Number)
                $user->name,                                   // Name
                $user->phone,                                  // Mobile
                $user->area->name ?? '',                       // Area
                $user->sales->sum('grand_total') ?? 0,         // Sale Total
                $user->total_paid ?? 0,                        // Sale Pay
                $user->total_due ?? 0,                         // Sale Due
                $user->advances() ?? 0,                        // Advance
                ($user->total_due ?? 0) - ($user->total_sale_return_due ?? 0), // Total Due
            ];
        }

        // Add totals row
        $rows[] = [
            '',                                                // SL
            '',                                                // Name
            '',                                                // Mobile
            'Total',                                           // Area column used for label
            $this->data['totalSale'] ?? 0,                     // Sale Total
            $this->data['pay'] ?? 0,                           // Sale Pay
            $this->data['total_due'] ?? 0,                     // Sale Due
            $this->data['total_advance'] ?? 0,                 // Advance
            ($this->data['total_due'] ?? 0) - ($this->data['total_return_due'] ?? 0), // Total Due
        ];

        return $rows;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name ?? 'Company Name'],            // Title rows
            ['Customer List'],
            ['Time: ' . now()->format('d-m-Y h:i A')],
            ['SL', 'Name', 'Mobile', 'Area', 'Total Sale', 'Sale Payment', 'Sale Due', 'Advance', 'Total Due'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'I';

        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:' . $lastCol . '1');  // Title
        $sheet->mergeCells('A2:' . $lastCol . '2');  // Subtitle
        $sheet->mergeCells('A3:' . $lastCol . '3');  // Time

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Center align title rows
        $sheet->getStyle('A1:' . $lastCol . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:' . $lastCol . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:' . $lastCol . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply bold to header row (row 4)
        $sheet->getStyle('A4:' . $lastCol . '4')->getFont()->setBold(true);
        $sheet->getStyle('A4:' . $lastCol . '4')->getAlignment()->setHorizontal('center');

        // Apply borders to data area (from row 4 to last row)
        $sheet->getStyle('A4:' . $lastCol . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Style the totals row (last row) - bold
        $sheet->getStyle('A' . $lastRow . ':' . $lastCol . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('D' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);   // SL
        $sheet->getColumnDimension('B')->setWidth(25);  // Name
        $sheet->getColumnDimension('C')->setWidth(15);  // Mobile
        $sheet->getColumnDimension('D')->setWidth(20);  // Area
        $sheet->getColumnDimension('E')->setWidth(12);  // Total Sale
        $sheet->getColumnDimension('F')->setWidth(12);  // Sale Payment
        $sheet->getColumnDimension('G')->setWidth(12);  // Sale Due
        $sheet->getColumnDimension('H')->setWidth(12);  // Advance
        $sheet->getColumnDimension('I')->setWidth(12);  // Total Due

        // Right align numeric columns (E to I) from row 5 to last row
        $sheet->getStyle('E5:' . $lastCol . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    public function title(): string
    {
        return 'Customer List';  // Sheet title
    }
}
