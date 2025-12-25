<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomersImport implements ToModel, WithStartRow
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
            return User::create($data);
        }
        return;
    }

    public function startRow(): int
    {
        return 6; // Skip the first 5 rows (headers and/or other data)
    }
}
