<?php

namespace Modules\Attendance\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Attendance\app\Models\WeekendSetup;

class WeekendSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weekendDays = [
            ['name' => 'Saturday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Sunday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Monday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Tuesday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Wednesday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Thursday', 'is_weekend' => false, 'status' => true],
            ['name' => 'Friday', 'is_weekend' => true, 'status' => true],
        ];

        foreach ($weekendDays as $weekendDay) {
            WeekendSetup::create($weekendDay);
        }
    }
}
