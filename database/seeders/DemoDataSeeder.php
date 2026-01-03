<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Ingredient\app\Models\Category;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\IngredientBrand;
use Modules\Ingredient\app\Models\UnitType;
use Modules\Supplier\app\Models\Supplier;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Unit Types first (if not exists)
        $units = $this->createUnitTypes();

        // Create Categories
        $categories = $this->createCategories();

        // Create Brands
        $brands = $this->createBrands();

        // Create Suppliers
        $this->createSuppliers();

        // Create Customers
        $this->createCustomers();

        // Create Products
        $this->createProducts($categories, $brands, $units);

        $this->command->info('Demo data seeded successfully!');
    }

    /**
     * Create Unit Types
     */
    private function createUnitTypes(): array
    {
        $unitTypes = [
            ['name' => 'Piece', 'ShortName' => 'pc', 'status' => 1],
            ['name' => 'Kilogram', 'ShortName' => 'kg', 'status' => 1],
            ['name' => 'Gram', 'ShortName' => 'g', 'status' => 1],
            ['name' => 'Liter', 'ShortName' => 'L', 'status' => 1],
            ['name' => 'Milliliter', 'ShortName' => 'ml', 'status' => 1],
        ];

        $units = [];
        foreach ($unitTypes as $unit) {
            $units[] = UnitType::firstOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }

        return $units;
    }

    /**
     * Create Categories
     */
    private function createCategories(): array
    {
        $categories = [
            ['name' => 'Beverages', 'status' => 1],
            ['name' => 'Snacks', 'status' => 1],
            ['name' => 'Dairy Products', 'status' => 1],
            ['name' => 'Bakery', 'status' => 1],
            ['name' => 'Fruits & Vegetables', 'status' => 1],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        return $createdCategories;
    }

    /**
     * Create Brands
     */
    private function createBrands(): array
    {
        $brands = [
            ['name' => 'Fresh Foods', 'description' => 'Quality fresh food products', 'status' => 1],
            ['name' => 'Ocean Breeze', 'description' => 'Premium beverage brand', 'status' => 1],
            ['name' => 'Golden Harvest', 'description' => 'Farm fresh products', 'status' => 1],
            ['name' => 'Sweet Delights', 'description' => 'Bakery and confectionery', 'status' => 1],
            ['name' => 'Nature Pure', 'description' => 'Organic and natural products', 'status' => 1],
        ];

        $createdBrands = [];
        foreach ($brands as $brand) {
            $createdBrands[] = IngredientBrand::firstOrCreate(
                ['name' => $brand['name']],
                $brand
            );
        }

        return $createdBrands;
    }

    /**
     * Create Suppliers
     */
    private function createSuppliers(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Trading Company',
                'company' => 'ABC Trading Co. Ltd.',
                'phone' => '01711111111',
                'email' => 'abc.trading@example.com',
                'address' => '123 Business District, Dhaka',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Global Supplies Ltd',
                'company' => 'Global Supplies Limited',
                'phone' => '01722222222',
                'email' => 'global.supplies@example.com',
                'address' => '456 Commercial Area, Chittagong',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Fresh Farm Distributors',
                'company' => 'Fresh Farm Dist. Inc.',
                'phone' => '01733333333',
                'email' => 'freshfarm@example.com',
                'address' => '789 Agricultural Zone, Sylhet',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Prime Wholesale',
                'company' => 'Prime Wholesale Traders',
                'phone' => '01744444444',
                'email' => 'prime.wholesale@example.com',
                'address' => '321 Market Street, Rajshahi',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Metro Suppliers',
                'company' => 'Metro Suppliers & Co.',
                'phone' => '01755555555',
                'email' => 'metro.suppliers@example.com',
                'address' => '654 Industrial Area, Khulna',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['phone' => $supplier['phone']],
                $supplier
            );
        }

        $this->command->info('5 Suppliers created.');
    }

    /**
     * Create Customers
     */
    private function createCustomers(): void
    {
        $customers = [
            [
                'name' => 'John Doe',
                'phone' => '01811111111',
                'email' => 'john.doe@example.com',
                'address' => '12 Residential Road, Dhaka',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '01822222222',
                'email' => 'jane.smith@example.com',
                'address' => '34 Green Street, Chittagong',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Robert Johnson',
                'phone' => '01833333333',
                'email' => 'robert.j@example.com',
                'address' => '56 Lake View, Sylhet',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Emily Brown',
                'phone' => '01844444444',
                'email' => 'emily.brown@example.com',
                'address' => '78 City Center, Rajshahi',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'name' => 'Michael Wilson',
                'phone' => '01855555555',
                'email' => 'michael.w@example.com',
                'address' => '90 Garden Avenue, Khulna',
                'status' => 1,
                'date' => now()->format('Y-m-d'),
            ],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }

        $this->command->info('5 Customers created.');
    }

    /**
     * Create Products
     */
    private function createProducts(array $categories, array $brands, array $units): void
    {
        $products = [
            [
                'name' => 'Orange Juice 1L',
                'short_description' => 'Fresh squeezed orange juice',
                'category_id' => $categories[0]->id ?? 1, // Beverages
                'brand_id' => $brands[1]->id ?? 1, // Ocean Breeze
                'unit_id' => $units[3]->id ?? 1, // Liter
                'cost' => 80,
                'stock' => 100,
                'stock_alert' => 10,
                'sku' => 'PROD-OJ001',
                'status' => 1,
                'stock_status' => 'in_stock',
            ],
            [
                'name' => 'Potato Chips 200g',
                'short_description' => 'Crispy salted potato chips',
                'category_id' => $categories[1]->id ?? 2, // Snacks
                'brand_id' => $brands[0]->id ?? 1, // Fresh Foods
                'unit_id' => $units[0]->id ?? 1, // Piece
                'cost' => 40,
                'stock' => 200,
                'stock_alert' => 20,
                'sku' => 'PROD-PC002',
                'status' => 1,
                'stock_status' => 'in_stock',
            ],
            [
                'name' => 'Fresh Milk 500ml',
                'short_description' => 'Pasteurized fresh cow milk',
                'category_id' => $categories[2]->id ?? 3, // Dairy Products
                'brand_id' => $brands[2]->id ?? 1, // Golden Harvest
                'unit_id' => $units[4]->id ?? 1, // Milliliter
                'cost' => 35,
                'stock' => 150,
                'stock_alert' => 15,
                'sku' => 'PROD-FM003',
                'status' => 1,
                'stock_status' => 'in_stock',
            ],
            [
                'name' => 'Whole Wheat Bread',
                'short_description' => 'Freshly baked whole wheat bread',
                'category_id' => $categories[3]->id ?? 4, // Bakery
                'brand_id' => $brands[3]->id ?? 1, // Sweet Delights
                'unit_id' => $units[0]->id ?? 1, // Piece
                'cost' => 45,
                'stock' => 80,
                'stock_alert' => 10,
                'sku' => 'PROD-WB004',
                'status' => 1,
                'stock_status' => 'in_stock',
            ],
            [
                'name' => 'Red Apple 1kg',
                'short_description' => 'Fresh organic red apples',
                'category_id' => $categories[4]->id ?? 5, // Fruits & Vegetables
                'brand_id' => $brands[4]->id ?? 1, // Nature Pure
                'unit_id' => $units[1]->id ?? 1, // Kilogram
                'cost' => 150,
                'stock' => 50,
                'stock_alert' => 5,
                'sku' => 'PROD-RA005',
                'status' => 1,
                'stock_status' => 'in_stock',
            ],
        ];

        foreach ($products as $product) {
            Ingredient::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        $this->command->info('5 Products created with categories, brands, and units.');
    }
}
