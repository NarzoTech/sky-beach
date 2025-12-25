<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BalanceTransferExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $index;
    public function __construct(private $transfers) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->transfers;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Balance Transfer List'],
            ['Time: ' . now()],
            [__('SN'), __('From Account'), __('To Account'), __('Amount'), __('Added By'), __('Date')],
        ];
    }
    public function map($balanceTransfer): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            accountList()[$balanceTransfer->fromAccount->account_type],
            accountList()[$balanceTransfer->toAccount->account_type],
            $balanceTransfer->amount,
            $balanceTransfer->createdBy->name,
            now()->parse($balanceTransfer->date)->format('d-m-Y'),
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
        $sheet->getStyle('A5:F' . $sheet->getHighestRow())
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


        // from c6 to end data will be right aligned
        // $sheet->getStyle('C6:C' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Balance Transfer List';  // Sheet title
    }
}
