<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Supplier\app\Models\Supplier;

class SuppliersImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $data  = [
            'name' => $row[1],
            'phone' => $row[2],
            'address' => $row[3],
        ];
        if ($row[1] != null) {
            return Supplier::create($data);
        }
        return;
    }
    public function startRow(): int
    {
        return 6; // Skip the first 5 rows (headers and/or other data)
    }
}
