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

class TablePerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $tables;
    private $data;

    public function __construct($tables, $data = null)
    {
        $this->tables = $tables;
        $this->data = $data;
    }

    public function collection()
    {
        return $this->tables;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            ['Table Performance Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Table Name'),
                __('Floor'),
                __('Capacity'),
                __('Total Orders'),
                __('Total Revenue'),
                __('Avg Order Value'),
            ],
        ];
    }

    public function map($table): array
    {
        $avgOrderValue = $table->total_orders > 0 ? $table->total_revenue / $table->total_orders : 0;

        return [
            ++$this->index,
            $table->table->name ?? 'N/A',
            $table->table->floor ?? 'N/A',
            $table->table->capacity ?? 'N/A',
            $table->total_orders,
            currency($table->total_revenue),
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
                $event->sheet->setCellValue('B' . $lastRow, '');
                $event->sheet->setCellValue('C' . $lastRow, '');
                $event->sheet->setCellValue('D' . $lastRow, 'Total');
                $event->sheet->setCellValue('E' . $lastRow, $totalOrders);
                $event->sheet->setCellValue('F' . $lastRow, currency($totalRevenue));
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
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);

        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Table Performance Report';
    }
}
