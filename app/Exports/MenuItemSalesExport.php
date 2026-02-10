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

class MenuItemSalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $items;
    private $data;

    public function __construct($items, $data = null)
    {
        $this->items = $items;
        $this->data = $data;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Menu Item Sales Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Menu Item'),
                __('Category'),
                __('Qty Sold'),
                __('Revenue'),
                __('Cost (COGS)'),
                __('Profit'),
                __('Profit %'),
            ],
        ];
    }

    public function map($item): array
    {
        $profitPercent = $item->total_revenue > 0 ? round(($item->total_profit / $item->total_revenue) * 100, 1) : 0;

        return [
            ++$this->index,
            $item->menuItem->name ?? 'N/A',
            $item->menuItem->category->name ?? 'N/A',
            $item->total_qty,
            currency($item->total_revenue),
            currency($item->total_cogs),
            currency($item->total_profit),
            $profitPercent . '%',
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $totalProfitPercent = ($data['totalRevenue'] ?? 0) > 0 ? round((($data['totalProfit'] ?? 0) / $data['totalRevenue']) * 100, 1) : 0;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, '');
                $event->sheet->setCellValue('C' . $lastRow, 'Total');
                $event->sheet->setCellValue('D' . $lastRow, $data['totalQty'] ?? 0);
                $event->sheet->setCellValue('E' . $lastRow, currency($data['totalRevenue'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalCogs'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, currency($data['totalProfit'] ?? 0));
                $event->sheet->setCellValue('H' . $lastRow, $totalProfitPercent . '%');
                $event->sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        $sheet->getStyle('A4:H' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);

        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Menu Item Sales Report';
    }
}
