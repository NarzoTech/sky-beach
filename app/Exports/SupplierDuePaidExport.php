<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupplierDuePaidExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private Collection|Arrayable $payments;

    public function __construct(Collection|Arrayable $payments)
    {
        $this->payments = $payments;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier Due Pay List'],
            ['Time: ' . now()],
            ['SL', 'Date', 'Invoice No', 'Supplier', 'Amount', 'Paid By']
        ];
    }
    public function map($payment): array
    {

        // Map the data to match your format
        return [
            $payment->id,
            now()->parse($payment->payment_date)->format('d M , Y'),
            $payment->purchase?->invoice_number,
            $payment->supplier->name,
            $payment->amount,
            $payment->createdBy->name
        ];
    }
    public function title(): string
    {
        return 'Supplier List';  // Sheet title
    }
}
