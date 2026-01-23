<?php

namespace Modules\TableManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\TableManagement\app\Models\RestaurantTable;

class TableManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Restaurant Tables...');

        // Only seed if no tables exist
        if (RestaurantTable::count() > 0) {
            $this->command->info('Tables already exist, skipping...');
            return;
        }

        $tables = [
            ['name' => 'Table 1', 'table_number' => 'T1', 'capacity' => 2, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 1],
            ['name' => 'Table 2', 'table_number' => 'T2', 'capacity' => 2, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 2],
            ['name' => 'Table 3', 'table_number' => 'T3', 'capacity' => 4, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 3],
            ['name' => 'Table 4', 'table_number' => 'T4', 'capacity' => 4, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 4],
            ['name' => 'Table 5', 'table_number' => 'T5', 'capacity' => 6, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 5],
            ['name' => 'Table 6', 'table_number' => 'T6', 'capacity' => 6, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 6],
            ['name' => 'Table 7', 'table_number' => 'T7', 'capacity' => 8, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 7],
            ['name' => 'Table 8', 'table_number' => 'T8', 'capacity' => 8, 'section' => 'Indoor', 'status' => 'available', 'sort_order' => 8],
            ['name' => 'Patio 1', 'table_number' => 'P1', 'capacity' => 4, 'section' => 'Outdoor', 'status' => 'available', 'sort_order' => 9],
            ['name' => 'Patio 2', 'table_number' => 'P2', 'capacity' => 4, 'section' => 'Outdoor', 'status' => 'available', 'sort_order' => 10],
            ['name' => 'Patio 3', 'table_number' => 'P3', 'capacity' => 6, 'section' => 'Outdoor', 'status' => 'available', 'sort_order' => 11],
            ['name' => 'VIP Room', 'table_number' => 'VIP1', 'capacity' => 10, 'section' => 'Private', 'status' => 'available', 'sort_order' => 12],
        ];

        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }

        $this->command->info(count($tables) . ' Restaurant Tables created.');
    }
}
