<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Ingredient\app\Models\Ingredient;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private $product) {}
    public function collection()
    {
        return $this->product->get();
    }

    public function headings(): array
    {
        return [
            ['name', 'sku', 'category', 'unit', 'brand', 'alert_quantity', 'barcode', 'purchase_price', 'selling_price', 'opening_stock_qty'],
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            $product->category->name,
            $product->unit->name,
            $product->brand->name,
            $product->stock_alert,
            $product->barcode,
            $product->cost,
            $product->price,
            $product->stock,
            $product->opening_stock_purchase_price_rate,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling for the header row
        $sheet->getStyle('A1:T1')->getFont()->setBold(true);
        $sheet->getStyle('A1:T1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Column Widths
        foreach (range('A', 'T') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
