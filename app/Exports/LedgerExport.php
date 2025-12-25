<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $ledgers;
    private $title;
    private $lastColumn;

    public function __construct($ledgers, $title)
    {
        $this->ledgers = $ledgers;
        $this->title = $title;
        $this->lastColumn = $title == 'Supplier Ledger' ? 'H' : 'G';
    }

    public function array(): array
    {
        $data = [];
        $opening = 0;
        $credit = 0;
        $debit = 0;
        $index = 0;

        foreach ($this->ledgers as $ledger) {
            $opening += $ledger->due_amount;
            $credit += $ledger->amount;
            $debit += $ledger->total_amount;

            $row = [
                ++$index,
                $ledger->invoice_no,
            ];

            if ($this->title == 'Supplier Ledger') {
                $row[] = $ledger->purchase?->memo_no;
            }

            $row = array_merge($row, [
                formatDate($ledger->date),
                $ledger->invoice_type,
                $ledger->amount,
                $ledger->total_amount,
                $opening,
            ]);

            $data[] = $row;
        }

        // Add total row
        $totalRow = [
            '',
            '',
        ];

        if ($this->title == 'Supplier Ledger') {
            $totalRow[] = '';
        }

        $totalRow = array_merge($totalRow, [
            '',
            __('Total'),
            $credit,
            $debit,
            $opening,
        ]);

        $data[] = $totalRow;

        return $data;
    }

    public function headings(): array
    {
        $setting = cache('setting');

        $headers = [
            __('SN'),
            __('Invoice No'),
        ];

        if ($this->title == 'Supplier Ledger') {
            $headers[] = __('Memo No');
        }

        $headers = array_merge($headers, [
            __('Date'),
            __('Description'),
            $this->title == 'Supplier Ledger'
                ? __('Paid') . ' (' . __('CREDIT') . ')'
                : __('Received') . ' (' . __('DEBIT') . ')',
            $this->title == 'Supplier Ledger'
                ? __('Product') . ' (' . __('DEBIT') . ')'
                : __('Sales') . ' (' . __('CREDIT') . ')',
            __('Balance') . ($this->title == 'Supplier Ledger' ? ' (' . __('DUE') . ')' : ''),
        ]);

        return [
            [$setting->app_name],
            [$this->title],
            ['Time: ' . now()->format('d-m-Y H:i:s')],
            $headers,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $this->lastColumn;
        $lastRow = $sheet->getHighestRow();

        // Merge cells for title and subtitle
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders to data rows
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Style the total row (last row) - make it bold
        $sheet->getStyle("A{$lastRow}:{$lastCol}{$lastRow}")->getFont()->setBold(true);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);

        if ($this->title == 'Supplier Ledger') {
            $sheet->getColumnDimension('H')->setWidth(20);
        }

        // Center align title rows
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A2:{$lastCol}2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A3:{$lastCol}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return $this->title;
    }
}
