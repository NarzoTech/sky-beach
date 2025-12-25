<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\Supplier\app\Services\SupplierService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SupplierExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{

    public function __construct(private $supplier) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Check if it's already a collection, otherwise execute the query
        if ($this->supplier instanceof \Illuminate\Support\Collection || $this->supplier instanceof \Illuminate\Database\Eloquent\Collection) {
            return $this->supplier;
        }
        return $this->supplier->get();
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier List'],
            ['Time: ' . now()],
            ['SL', 'Name', 'Mobile', 'Area', 'Purchase', '', 'Purchase Return', ''],
            ['', '', '', '', 'Total', 'Pay', 'Total', 'Pay', 'Advance', 'Total Due']
        ];
    }

    public function map($supplier): array
    {
        $totalReturn = $supplier->purchaseReturn->sum('return_amount');
        $totalReturnPaid = $supplier->purchaseReturn->sum(
            'received_amount',
        );
        // Map the data to match your format
        return [
            $supplier->id,                        // SL
            $supplier->name,                      // Name
            $supplier->phone,                    // Mobile
            $supplier->area->name,                      // Area
            $supplier->total_purchase ?? 0,            // Purchase Total
            $supplier->total_paid ?? 0,              // Purchase Pay
            $totalReturn ?? 0,     // Purchase Return Total
            $totalReturnPaid ?? 0,       // Purchase Return Pay
            $supplier->advance ?? 0,                   // Advance
            $supplier->total_due - $totalReturn ?? 0,                 // Total Due
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');  // Title
        $sheet->mergeCells('A2:J2');  // Subtitle
        $sheet->mergeCells('A3:J3');  // Time

        // Merge cells for top-level headers
        $sheet->mergeCells('E4:F4');  // Purchase spans 'Total' and 'Pay'
        $sheet->mergeCells('G4:H4');  // Purchase Return spans 'Total' and 'Pay'

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A5:J6')->getFont()->setBold(true);
        $sheet->getStyle('A5:J6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
    }

    public function title(): string
    {
        return 'Supplier List';  // Sheet title
    }
}
