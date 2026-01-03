<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Ingredient\app\Models\Ingredient;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IngredientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private $ingredient) {}
    public function collection()
    {
        return $this->ingredient->get();
    }

    public function headings(): array
    {
        return [
            ['name', 'code', 'category', 'purchase_unit', 'consumption_unit', 'conversion_rate', 'purchase_price', 'cost_per_unit', 'low_qty', 'brand', 'opening_stock_qty'],
        ];
    }

    public function map($ingredient): array
    {
        return [
            $ingredient->name,
            $ingredient->sku,
            $ingredient->category->name,
            $ingredient->purchaseUnit->name ?? $ingredient->unit->name,
            $ingredient->consumptionUnit->name ?? '',
            $ingredient->conversion_rate ?? 1,
            $ingredient->purchase_price ?? $ingredient->cost,
            $ingredient->consumption_unit_cost ?? 0,
            $ingredient->stock_alert,
            $ingredient->brand->name,
            $ingredient->stock,
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
