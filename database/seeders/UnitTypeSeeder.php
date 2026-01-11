<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ingredient\app\Models\UnitType;

/**
 * UnitTypeSeeder - Seeds common unit types for ingredient management
 *
 * This seeder creates a hierarchical unit structure with base units
 * and their child units for proper conversion support.
 *
 * Usage: php artisan db:seed --class=UnitTypeSeeder
 */
class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Unit Types...');

        // ==================== WEIGHT UNITS ====================
        $kilogram = $this->createUnit([
            'name' => 'Kilogram',
            'ShortName' => 'Kg',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Gram',
            'ShortName' => 'g',
            'base_unit' => $kilogram->id,
            'operator' => '/',
            'operator_value' => 1000,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Milligram',
            'ShortName' => 'mg',
            'base_unit' => $kilogram->id,
            'operator' => '/',
            'operator_value' => 1000000,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Pound',
            'ShortName' => 'lb',
            'base_unit' => $kilogram->id,
            'operator' => '/',
            'operator_value' => 2.20462,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Ounce',
            'ShortName' => 'oz',
            'base_unit' => $kilogram->id,
            'operator' => '/',
            'operator_value' => 35.274,
            'status' => 1,
        ]);

        // ==================== VOLUME UNITS ====================
        $liter = $this->createUnit([
            'name' => 'Liter',
            'ShortName' => 'L',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Milliliter',
            'ShortName' => 'ml',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 1000,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Centiliter',
            'ShortName' => 'cl',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 100,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Gallon',
            'ShortName' => 'gal',
            'base_unit' => $liter->id,
            'operator' => '*',
            'operator_value' => 3.78541,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Fluid Ounce',
            'ShortName' => 'fl oz',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 33.814,
            'status' => 1,
        ]);

        // ==================== PIECE/COUNT UNITS ====================
        $piece = $this->createUnit([
            'name' => 'Piece',
            'ShortName' => 'pc',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Dozen',
            'ShortName' => 'dz',
            'base_unit' => $piece->id,
            'operator' => '*',
            'operator_value' => 12,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Half Dozen',
            'ShortName' => '1/2 dz',
            'base_unit' => $piece->id,
            'operator' => '*',
            'operator_value' => 6,
            'status' => 1,
        ]);

        $this->createUnit([
            'name' => 'Pair',
            'ShortName' => 'pr',
            'base_unit' => $piece->id,
            'operator' => '*',
            'operator_value' => 2,
            'status' => 1,
        ]);

        // ==================== PACKAGE UNITS ====================
        $pack = $this->createUnit([
            'name' => 'Pack',
            'ShortName' => 'pk',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        $box = $this->createUnit([
            'name' => 'Box',
            'ShortName' => 'box',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        $carton = $this->createUnit([
            'name' => 'Carton',
            'ShortName' => 'ctn',
            'base_unit' => null,
            'operator' => '*',
            'operator_value' => 1,
            'status' => 1,
        ]);

        // ==================== SPOON MEASURES ====================
        $tablespoon = $this->createUnit([
            'name' => 'Tablespoon',
            'ShortName' => 'tbsp',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 67.628, // ~15ml per tbsp
            'status' => 1,
        ]);

        $teaspoon = $this->createUnit([
            'name' => 'Teaspoon',
            'ShortName' => 'tsp',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 202.884, // ~5ml per tsp
            'status' => 1,
        ]);

        // ==================== CUP MEASURES ====================
        $this->createUnit([
            'name' => 'Cup',
            'ShortName' => 'cup',
            'base_unit' => $liter->id,
            'operator' => '/',
            'operator_value' => 4.22675, // ~237ml per cup
            'status' => 1,
        ]);

        $this->command->info('Unit Types seeded successfully!');
        $this->command->info('');
        $this->command->table(
            ['ID', 'Name', 'Short', 'Base Unit', 'Operator', 'Value'],
            UnitType::with('parent')->get()->map(fn($u) => [
                $u->id,
                $u->name,
                $u->ShortName,
                $u->parent?->name ?? '-',
                $u->operator,
                $u->operator_value,
            ])->toArray()
        );
    }

    /**
     * Create or update a unit type
     */
    private function createUnit(array $data): UnitType
    {
        return UnitType::updateOrCreate(
            ['name' => $data['name']],
            $data
        );
    }
}
