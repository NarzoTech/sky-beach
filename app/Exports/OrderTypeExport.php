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

class OrderTypeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $orderTypes;
    private $data;

    public function __construct($orderTypes, $data = null)
    {
        $this->orderTypes = $orderTypes;
        $this->data = $data;
    }

    public function collection()
    {
        return $this->orderTypes;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Order Type Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Order Type'),
                __('Total Orders'),
                __('Total Revenue'),
                __('Total Cost'),
                __('Profit'),
                __('% of Total'),
            ],
        ];
    }

    public function map($type): array
    {
        $orderTypeLabels = [
            'dine_in' => __('Dine In'),
            'take_away' => __('Take Away'),
            'website' => __('Website'),
        ];

        $grandTotal = $this->data['totalOrders'] ?? 0;
        $percentage = $grandTotal > 0 ? round(($type->total_orders / $grandTotal) * 100, 1) : 0;

        return [
            ++$this->index,
            $orderTypeLabels[$type->order_type] ?? ucfirst(str_replace('_', ' ', $type->order_type)),
            $type->total_orders,
            currency($type->total_revenue),
            currency($type->total_cogs),
            currency($type->total_profit),
            $percentage . '%',
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, 'Total');
                $event->sheet->setCellValue('C' . $lastRow, $data['totalOrders'] ?? 0);
                $event->sheet->setCellValue('D' . $lastRow, currency($data['totalRevenue'] ?? 0));
                $event->sheet->setCellValue('E' . $lastRow, currency($data['totalCogs'] ?? 0));
                $event->sheet->setCellValue('F' . $lastRow, currency($data['totalProfit'] ?? 0));
                $event->sheet->setCellValue('G' . $lastRow, '100%');
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
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(12);

        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Order Type Report';
    }
}
