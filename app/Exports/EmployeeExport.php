<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $index;
    public function __construct(private $employees) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->employees;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Employee List'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Employee Name'),
                __('Designation'),
                __('Phone'),
                __('Email'),
                __('Base Salary'),
                __('Joining Date'),
            ],
        ];
    }
    public function map($employee): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $employee->name,
            $employee->designation,
            $employee->mobile,
            $employee->email,
            $employee->salary,
            date('d-m-Y', strtotime($employee->join_date)) ?? __('N/A'),
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:G1');  // Title
        $sheet->mergeCells('A2:G2');  // Subtitle
        $sheet->mergeCells('A3:G3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Employee List';
    }
}
