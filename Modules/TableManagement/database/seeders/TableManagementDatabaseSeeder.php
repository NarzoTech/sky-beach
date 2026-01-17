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
            ['name' => 'Table 1', 'capacity' => 2, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 1],
            ['name' => 'Table 2', 'capacity' => 2, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 2],
            ['name' => 'Table 3', 'capacity' => 4, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 3],
            ['name' => 'Table 4', 'capacity' => 4, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 4],
            ['name' => 'Table 5', 'capacity' => 6, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 5],
            ['name' => 'Table 6', 'capacity' => 6, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 6],
            ['name' => 'Table 7', 'capacity' => 8, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 7],
            ['name' => 'Table 8', 'capacity' => 8, 'location' => 'Indoor', 'status' => 'available', 'sort_order' => 8],
            ['name' => 'Patio 1', 'capacity' => 4, 'location' => 'Outdoor', 'status' => 'available', 'sort_order' => 9],
            ['name' => 'Patio 2', 'capacity' => 4, 'location' => 'Outdoor', 'status' => 'available', 'sort_order' => 10],
            ['name' => 'Patio 3', 'capacity' => 6, 'location' => 'Outdoor', 'status' => 'available', 'sort_order' => 11],
            ['name' => 'VIP Room', 'capacity' => 10, 'location' => 'Private', 'status' => 'available', 'sort_order' => 12],
        ];

        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }

        $this->command->info(count($tables) . ' Restaurant Tables created.');
    }
}
