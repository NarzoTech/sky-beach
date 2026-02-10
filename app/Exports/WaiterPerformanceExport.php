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

class WaiterPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $waiters;
    private $data;

    public function __construct($waiters, $data = null)
    {
        $this->waiters = $waiters;
        $this->data = $data;
    }

    public function collection()
    {
        return $this->waiters;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Waiter Performance Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Waiter Name'),
                __('Total Orders'),
                __('Total Revenue'),
                __('Total Cost'),
                __('Profit'),
                __('Avg Order Value'),
            ],
        ];
    }

    public function map($waiter): array
    {
        $avgOrderValue = $waiter->total_orders > 0 ? $waiter->total_revenue / $waiter->total_orders : 0;

        return [
            ++$this->index,
            $waiter->waiter->name ?? 'N/A',
            $waiter->total_orders,
            currency($waiter->total_revenue),
            currency($waiter->total_cogs),
            currency($waiter->total_profit),
            currency($avgOrderValue),
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $totalOrders = $data['totalOrders'] ?? 0;
                $totalRevenue = $data['totalRevenue'] ?? 0;
                $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, 'Total');
                $event->sheet->setCellValue('C' . $lastRow, $totalOrders);
                $event->sheet->setCellValue('D' . $lastRow, currency($totalRevenue));
                $event->sheet->setCellValue('E' . $lastRow, currency($data['totalCogs'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalProfit'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, currency($avgOrder));
                $event->sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        $sheet->getStyle('A4:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);

        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Waiter Performance Report';
    }
}
