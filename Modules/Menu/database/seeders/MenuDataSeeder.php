<?php

namespace Modules\Menu\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\ComboItem;
use Illuminate\Support\Facades\DB;

class MenuDataSeeder extends Seeder
{
    // Store created records for reference
    private $categories = [];
    private $addons = [];
    private $menuItems = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (optional - comment out if you want to keep existing data)
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Delete all records first
            DB::table('combo_items')->delete();
            DB::table('combos')->delete();
            DB::table('menu_item_addons')->delete();
            DB::table('menu_variants')->delete();
            DB::table('recipes')->delete();
            DB::table('menu_item_translations')->delete();
            DB::table('menu_items')->delete();
            DB::table('menu_addons')->delete();
            DB::table('menu_category_translations')->delete();
            DB::table('menu_categories')->delete();
            
            // Then truncate
            DB::table('combo_items')->truncate();
            DB::table('combos')->truncate();
            DB::table('menu_item_addons')->truncate();
            DB::table('menu_variants')->truncate();
            DB::table('recipes')->truncate();
            DB::table('menu_item_translations')->truncate();
            DB::table('menu_items')->truncate();
            DB::table('menu_addons')->truncate();
            DB::table('menu_category_translations')->truncate();
            DB::table('menu_categories')->truncate();
            
            // Reset auto-increment counters
            DB::statement('ALTER TABLE menu_categories AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE menu_addons AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE menu_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE menu_variants AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE combos AUTO_INCREMENT = 1');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Verify cleanup
            $categoriesCount = DB::table('menu_categories')->count();
            $itemsCount = DB::table('menu_items')->count();
            $addonsCount = DB::table('menu_addons')->count();
            
            $this->command->info('Database cleaned and auto-increment reset.');
            $this->command->info("Verification - Categories: {$categoriesCount}, Items: {$itemsCount}, Addons: {$addonsCount}");

            $this->command->info('Seeding Menu Categories...');
            $this->seedMenuCategories();
            
            $this->command->info('Seeding Menu Addons...');
            $this->seedMenuAddons();
            
            $this->command->info('Seeding Menu Items...');
            $this->seedMenuItems();
            $this->command->info('First menu item ID: ' . $this->menuItems[0]->id);
            $this->command->info('Last menu item ID: ' . $this->menuItems[20]->id);
            $this->command->info('Nachos ID (index 2): ' . $this->menuItems[2]->id);
            
            $this->command->info('Seeding Menu Variants...');
            $this->seedMenuVariants();
            
            $this->command->info('Seeding Menu Item Addons...');
            $this->command->info('Total Menu Items: ' . count($this->menuItems));
            $this->command->info('Total Addons: ' . count($this->addons));
            $this->seedMenuItemAddons();
            
            $this->command->info('Seeding Combos...');
            $this->seedCombos();
            
            $this->command->info('Menu seeding completed successfully!');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->error('Error during seeding: ' . $e->getMessage());
            $this->command->error('Line: ' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Seed menu categories
     */
    private function seedMenuCategories(): void
    {
        $categories = [
            ['name' => 'Appetizers', 'slug' => 'appetizers', 'description' => 'Start your meal with our delicious appetizers', 'display_order' => 1, 'status' => 1, 'is_featured' => 1],
            ['name' => 'Main Course', 'slug' => 'main-course', 'description' => 'Our signature main dishes', 'display_order' => 2, 'status' => 1, 'is_featured' => 1],
            ['name' => 'Pasta', 'slug' => 'pasta', 'description' => 'Italian pasta dishes', 'display_order' => 3, 'status' => 1, 'is_featured' => 0],
            ['name' => 'Pizza', 'slug' => 'pizza', 'description' => 'Wood-fired pizzas with fresh ingredients', 'display_order' => 4, 'status' => 1, 'is_featured' => 1],
            ['name' => 'Burgers', 'slug' => 'burgers', 'description' => 'Juicy burgers with premium ingredients', 'display_order' => 5, 'status' => 1, 'is_featured' => 0],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet endings to your meal', 'display_order' => 6, 'status' => 1, 'is_featured' => 0],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Refreshing drinks and beverages', 'display_order' => 7, 'status' => 1, 'is_featured' => 0],
            ['name' => 'Salads', 'slug' => 'salads', 'description' => 'Fresh and healthy salads', 'display_order' => 8, 'status' => 1, 'is_featured' => 0],
        ];

        foreach ($categories as $category) {
            $this->categories[] = MenuCategory::create($category);
        }
    }

    /**
     * Seed menu addons
     */
    private function seedMenuAddons(): void
    {
        $addons = [
            ['name' => 'Extra Cheese', 'description' => 'Additional cheese topping', 'price' => 2.50, 'status' => 1],
            ['name' => 'Bacon', 'description' => 'Crispy bacon strips', 'price' => 3.00, 'status' => 1],
            ['name' => 'Avocado', 'description' => 'Fresh avocado slices', 'price' => 2.00, 'status' => 1],
            ['name' => 'Mushrooms', 'description' => 'Sautéed mushrooms', 'price' => 2.00, 'status' => 1],
            ['name' => 'Jalapeños', 'description' => 'Spicy jalapeño peppers', 'price' => 1.50, 'status' => 1],
            ['name' => 'Fried Egg', 'description' => 'Perfectly fried egg', 'price' => 1.50, 'status' => 1],
            ['name' => 'Grilled Chicken', 'description' => 'Seasoned grilled chicken', 'price' => 4.00, 'status' => 1],
            ['name' => 'Pepperoni', 'description' => 'Spicy pepperoni slices', 'price' => 2.50, 'status' => 1],
            ['name' => 'Olives', 'description' => 'Black or green olives', 'price' => 1.50, 'status' => 1],
            ['name' => 'Onions', 'description' => 'Caramelized onions', 'price' => 1.00, 'status' => 1],
            ['name' => 'Bell Peppers', 'description' => 'Fresh bell peppers', 'price' => 1.50, 'status' => 1],
            ['name' => 'Guacamole', 'description' => 'Homemade guacamole', 'price' => 3.00, 'status' => 1],
            ['name' => 'Sour Cream', 'description' => 'Fresh sour cream', 'price' => 1.00, 'status' => 1],
            ['name' => 'Extra Sauce', 'description' => 'Additional sauce of your choice', 'price' => 0.50, 'status' => 1],
        ];

        foreach ($addons as $addon) {
            $this->addons[] = MenuAddon::create($addon);
        }
    }

    /**
     * Seed menu items
     */
    private function seedMenuItems(): void
    {
        // Appetizers
        $this->menuItems[] = MenuItem::create(['name' => 'Chicken Wings', 'slug' => 'chicken-wings', 'short_description' => 'Crispy chicken wings with your choice of sauce', 'long_description' => 'Perfectly seasoned and fried chicken wings served with celery sticks and blue cheese dressing. Choose from buffalo, BBQ, honey garlic, or teriyaki sauce.', 'category_id' => $this->categories[0]->id, 'base_price' => 12.99, 'cost_price' => 5.50, 'preparation_time' => 15, 'calories' => 450, 'is_spicy' => 1, 'spice_level' => 3, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Mozzarella Sticks', 'slug' => 'mozzarella-sticks', 'short_description' => 'Breaded mozzarella cheese sticks', 'long_description' => 'Golden fried mozzarella sticks served with marinara sauce for dipping.', 'category_id' => $this->categories[0]->id, 'base_price' => 8.99, 'cost_price' => 3.20, 'preparation_time' => 10, 'calories' => 380, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        $this->menuItems[] = MenuItem::create(['name' => 'Nachos Supreme', 'slug' => 'nachos-supreme', 'short_description' => 'Loaded nachos with all the toppings', 'long_description' => 'Crispy tortilla chips topped with melted cheese, jalapeños, sour cream, guacamole, salsa, and your choice of protein.', 'category_id' => $this->categories[0]->id, 'base_price' => 11.99, 'cost_price' => 4.80, 'preparation_time' => 12, 'calories' => 620, 'is_spicy' => 1, 'spice_level' => 2, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 3]);
        
        // Main Course
        $this->menuItems[] = MenuItem::create(['name' => 'Grilled Salmon', 'slug' => 'grilled-salmon', 'short_description' => 'Fresh Atlantic salmon grilled to perfection', 'long_description' => 'Premium Atlantic salmon fillet grilled and served with roasted vegetables and lemon butter sauce.', 'category_id' => $this->categories[1]->id, 'base_price' => 24.99, 'cost_price' => 12.00, 'preparation_time' => 20, 'calories' => 520, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Ribeye Steak', 'slug' => 'ribeye-steak', 'short_description' => 'Premium ribeye steak cooked to your liking', 'long_description' => '12oz premium ribeye steak seasoned and grilled to your preference. Served with mashed potatoes and seasonal vegetables.', 'category_id' => $this->categories[1]->id, 'base_price' => 32.99, 'cost_price' => 16.50, 'preparation_time' => 25, 'calories' => 780, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        
        // Pasta
        $this->menuItems[] = MenuItem::create(['name' => 'Spaghetti Carbonara', 'slug' => 'spaghetti-carbonara', 'short_description' => 'Classic Italian pasta with creamy sauce', 'long_description' => 'Traditional Italian spaghetti with bacon, eggs, parmesan cheese, and black pepper in a creamy sauce.', 'category_id' => $this->categories[2]->id, 'base_price' => 16.99, 'cost_price' => 6.50, 'preparation_time' => 18, 'calories' => 680, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Penne Arrabbiata', 'slug' => 'penne-arrabbiata', 'short_description' => 'Spicy tomato pasta with garlic', 'long_description' => 'Penne pasta in a spicy tomato sauce with garlic, red chili peppers, and fresh basil.', 'category_id' => $this->categories[2]->id, 'base_price' => 14.99, 'cost_price' => 5.20, 'preparation_time' => 15, 'calories' => 540, 'is_vegetarian' => 1, 'is_vegan' => 1, 'is_spicy' => 1, 'spice_level' => 4, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        
        // Pizza
        $this->menuItems[] = MenuItem::create(['name' => 'Margherita Pizza', 'slug' => 'margherita-pizza', 'short_description' => 'Classic Italian pizza with tomato and mozzarella', 'long_description' => 'Traditional Neapolitan pizza with San Marzano tomato sauce, fresh mozzarella, basil, and extra virgin olive oil.', 'category_id' => $this->categories[3]->id, 'base_price' => 13.99, 'cost_price' => 4.50, 'preparation_time' => 12, 'calories' => 580, 'is_vegetarian' => 1, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Pepperoni Pizza', 'slug' => 'pepperoni-pizza', 'short_description' => 'Classic pepperoni pizza', 'long_description' => 'Our signature pizza topped with tomato sauce, mozzarella cheese, and generous amounts of pepperoni.', 'category_id' => $this->categories[3]->id, 'base_price' => 15.99, 'cost_price' => 5.80, 'preparation_time' => 12, 'calories' => 680, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        $this->menuItems[] = MenuItem::create(['name' => 'Vegetarian Supreme Pizza', 'slug' => 'vegetarian-supreme-pizza', 'short_description' => 'Loaded with fresh vegetables', 'long_description' => 'Pizza topped with bell peppers, mushrooms, onions, olives, tomatoes, and mozzarella cheese.', 'category_id' => $this->categories[3]->id, 'base_price' => 16.99, 'cost_price' => 6.20, 'preparation_time' => 15, 'calories' => 620, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 3]);
        
        // Burgers
        $this->menuItems[] = MenuItem::create(['name' => 'Classic Beef Burger', 'slug' => 'classic-beef-burger', 'short_description' => 'Juicy beef patty with fresh toppings', 'long_description' => 'Half-pound beef patty with lettuce, tomato, onion, pickles, and special sauce on a toasted bun. Served with fries.', 'category_id' => $this->categories[4]->id, 'base_price' => 14.99, 'cost_price' => 6.00, 'preparation_time' => 15, 'calories' => 720, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Chicken Burger', 'slug' => 'chicken-burger', 'short_description' => 'Grilled chicken breast burger', 'long_description' => 'Grilled chicken breast with lettuce, tomato, and mayo on a whole wheat bun. Served with sweet potato fries.', 'category_id' => $this->categories[4]->id, 'base_price' => 13.99, 'cost_price' => 5.50, 'preparation_time' => 15, 'calories' => 580, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        $this->menuItems[] = MenuItem::create(['name' => 'Veggie Burger', 'slug' => 'veggie-burger', 'short_description' => 'Plant-based burger patty', 'long_description' => 'House-made veggie patty with lettuce, tomato, avocado, and chipotle mayo. Served with regular fries.', 'category_id' => $this->categories[4]->id, 'base_price' => 12.99, 'cost_price' => 4.80, 'preparation_time' => 12, 'calories' => 520, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 3]);
        
        // Desserts
        $this->menuItems[] = MenuItem::create(['name' => 'Chocolate Lava Cake', 'slug' => 'chocolate-lava-cake', 'short_description' => 'Warm chocolate cake with molten center', 'long_description' => 'Decadent chocolate cake with a gooey molten chocolate center. Served warm with vanilla ice cream.', 'category_id' => $this->categories[5]->id, 'base_price' => 7.99, 'cost_price' => 2.50, 'preparation_time' => 8, 'calories' => 420, 'is_vegetarian' => 1, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Tiramisu', 'slug' => 'tiramisu', 'short_description' => 'Classic Italian dessert', 'long_description' => 'Traditional Italian dessert made with ladyfingers, espresso, mascarpone cheese, and cocoa powder.', 'category_id' => $this->categories[5]->id, 'base_price' => 8.99, 'cost_price' => 3.20, 'preparation_time' => 5, 'calories' => 380, 'is_vegetarian' => 1, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        $this->menuItems[] = MenuItem::create(['name' => 'Cheesecake', 'slug' => 'cheesecake', 'short_description' => 'New York style cheesecake', 'long_description' => 'Creamy New York style cheesecake with graham cracker crust. Choice of strawberry or chocolate topping.', 'category_id' => $this->categories[5]->id, 'base_price' => 6.99, 'cost_price' => 2.20, 'preparation_time' => 5, 'calories' => 350, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 3]);
        
        // Beverages
        $this->menuItems[] = MenuItem::create(['name' => 'Fresh Orange Juice', 'slug' => 'fresh-orange-juice', 'short_description' => 'Freshly squeezed orange juice', 'long_description' => '100% fresh squeezed orange juice, no added sugar or preservatives.', 'category_id' => $this->categories[6]->id, 'base_price' => 4.99, 'cost_price' => 1.50, 'preparation_time' => 3, 'calories' => 110, 'is_vegetarian' => 1, 'is_vegan' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Iced Coffee', 'slug' => 'iced-coffee', 'short_description' => 'Cold brewed coffee over ice', 'long_description' => 'Cold brewed premium coffee served over ice with your choice of milk and sweetener.', 'category_id' => $this->categories[6]->id, 'base_price' => 4.49, 'cost_price' => 1.20, 'preparation_time' => 3, 'calories' => 80, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
        $this->menuItems[] = MenuItem::create(['name' => 'Smoothie Bowl', 'slug' => 'smoothie-bowl', 'short_description' => 'Refreshing fruit smoothie', 'long_description' => 'Blended tropical fruits including mango, pineapple, banana, and coconut water.', 'category_id' => $this->categories[6]->id, 'base_price' => 6.99, 'cost_price' => 2.80, 'preparation_time' => 5, 'calories' => 280, 'is_vegetarian' => 1, 'is_vegan' => 1, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 3]);
        
        // Salads
        $this->menuItems[] = MenuItem::create(['name' => 'Caesar Salad', 'slug' => 'caesar-salad', 'short_description' => 'Classic Caesar salad', 'long_description' => 'Crisp romaine lettuce with Caesar dressing, parmesan cheese, croutons, and optional grilled chicken.', 'category_id' => $this->categories[7]->id, 'base_price' => 10.99, 'cost_price' => 4.20, 'preparation_time' => 8, 'calories' => 320, 'is_vegetarian' => 1, 'is_featured' => 1, 'is_available' => 1, 'status' => 1, 'display_order' => 1]);
        $this->menuItems[] = MenuItem::create(['name' => 'Greek Salad', 'slug' => 'greek-salad', 'short_description' => 'Mediterranean fresh salad', 'long_description' => 'Fresh tomatoes, cucumbers, red onions, kalamata olives, and feta cheese with oregano and olive oil.', 'category_id' => $this->categories[7]->id, 'base_price' => 11.99, 'cost_price' => 4.50, 'preparation_time' => 8, 'calories' => 280, 'is_vegetarian' => 1, 'is_featured' => 0, 'is_available' => 1, 'status' => 1, 'display_order' => 2]);
    }

    /**
     * Seed menu variants
     */
    private function seedMenuVariants(): void
    {
        // Pizza sizes (Margherita, Pepperoni, Vegetarian Supreme)
        foreach ([7, 8, 9] as $index) {
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Small (10")', 'price_adjustment' => -3.00, 'is_default' => 0, 'status' => 1]);
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Medium (12")', 'price_adjustment' => 0.00, 'is_default' => 1, 'status' => 1]);
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Large (14")', 'price_adjustment' => 4.00, 'is_default' => 0, 'status' => 1]);
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Extra Large (16")', 'price_adjustment' => 7.00, 'is_default' => 0, 'status' => 1]);
        }

        // Beverage sizes (Fresh Orange Juice, Iced Coffee)
        foreach ([16, 17] as $index) {
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Small (12oz)', 'price_adjustment' => -1.00, 'is_default' => 0, 'status' => 1]);
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Medium (16oz)', 'price_adjustment' => 0.00, 'is_default' => 1, 'status' => 1]);
            MenuVariant::create(['menu_item_id' => $this->menuItems[$index]->id, 'name' => 'Large (20oz)', 'price_adjustment' => 1.50, 'is_default' => 0, 'status' => 1]);
        }

        // Smoothie sizes
        MenuVariant::create(['menu_item_id' => $this->menuItems[18]->id, 'name' => 'Regular', 'price_adjustment' => 0.00, 'is_default' => 1, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[18]->id, 'name' => 'Large', 'price_adjustment' => 2.50, 'is_default' => 0, 'status' => 1]);

        // Steak doneness (Ribeye Steak)
        MenuVariant::create(['menu_item_id' => $this->menuItems[4]->id, 'name' => 'Rare', 'price_adjustment' => 0.00, 'is_default' => 0, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[4]->id, 'name' => 'Medium Rare', 'price_adjustment' => 0.00, 'is_default' => 1, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[4]->id, 'name' => 'Medium', 'price_adjustment' => 0.00, 'is_default' => 0, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[4]->id, 'name' => 'Medium Well', 'price_adjustment' => 0.00, 'is_default' => 0, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[4]->id, 'name' => 'Well Done', 'price_adjustment' => 0.00, 'is_default' => 0, 'status' => 1]);

        // Chicken Wings portions
        MenuVariant::create(['menu_item_id' => $this->menuItems[0]->id, 'name' => '6 Pieces', 'price_adjustment' => -3.00, 'is_default' => 0, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[0]->id, 'name' => '10 Pieces', 'price_adjustment' => 0.00, 'is_default' => 1, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[0]->id, 'name' => '15 Pieces', 'price_adjustment' => 5.00, 'is_default' => 0, 'status' => 1]);
        MenuVariant::create(['menu_item_id' => $this->menuItems[0]->id, 'name' => '20 Pieces', 'price_adjustment' => 9.00, 'is_default' => 0, 'status' => 1]);
    }

    /**
     * Seed menu item addons
     */
    private function seedMenuItemAddons(): void
    {
        // Verify we have enough menu items and addons
        if (count($this->menuItems) < 21) {
            throw new \Exception('Not enough menu items created. Expected 21, got ' . count($this->menuItems));
        }
        if (count($this->addons) < 14) {
            throw new \Exception('Not enough addons created. Expected 14, got ' . count($this->addons));
        }

        // Pizza addons (Margherita, Pepperoni, Vegetarian Supreme)
        $pizzaAddons = [
            [$this->addons[0]->id, 2], // Extra Cheese
            [$this->addons[3]->id, 1], // Mushrooms
            [$this->addons[7]->id, 1], // Pepperoni
            [$this->addons[8]->id, 1], // Olives
            [$this->addons[10]->id, 1], // Bell Peppers
        ];
        
        foreach ([7, 8, 9] as $pizzaIndex) {
            if (!isset($this->menuItems[$pizzaIndex])) {
                throw new \Exception("Menu item at index {$pizzaIndex} does not exist");
            }
            foreach ($pizzaAddons as $addon) {
                DB::table('menu_item_addons')->insert([
                    'menu_item_id' => $this->menuItems[$pizzaIndex]->id,
                    'addon_id' => $addon[0],
                    'max_quantity' => $addon[1],
                    'is_required' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Burger addons (Classic Beef, Chicken, Veggie)
        $burgerAddons = [
            [$this->addons[0]->id, 2], // Extra Cheese
            [$this->addons[1]->id, 2], // Bacon
            [$this->addons[2]->id, 1], // Avocado
            [$this->addons[3]->id, 1], // Mushrooms
            [$this->addons[4]->id, 1], // Jalapeños
            [$this->addons[5]->id, 1], // Fried Egg
        ];
        
        foreach ([10, 11, 12] as $burgerIndex) {
            if (!isset($this->menuItems[$burgerIndex])) {
                throw new \Exception("Menu item at index {$burgerIndex} does not exist");
            }
            foreach ($burgerAddons as $addon) {
                DB::table('menu_item_addons')->insert([
                    'menu_item_id' => $this->menuItems[$burgerIndex]->id,
                    'addon_id' => $addon[0],
                    'max_quantity' => $addon[1],
                    'is_required' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Nachos addons
        if (!isset($this->menuItems[2])) {
            throw new \Exception("Nachos (index 2) does not exist. Total items: " . count($this->menuItems));
        }
        $this->command->info("Inserting Nachos addons. Nachos ID: {$this->menuItems[2]->id}");
        DB::table('menu_item_addons')->insert([
            ['menu_item_id' => $this->menuItems[2]->id, 'addon_id' => $this->addons[6]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Grilled Chicken
            ['menu_item_id' => $this->menuItems[2]->id, 'addon_id' => $this->addons[11]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Guacamole
            ['menu_item_id' => $this->menuItems[2]->id, 'addon_id' => $this->addons[12]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Sour Cream
        ]);

        // Salad addons (Caesar, Greek)
        foreach ([19, 20] as $saladIndex) {
            if (!isset($this->menuItems[$saladIndex])) {
                throw new \Exception("Menu item at index {$saladIndex} does not exist. Total items: " . count($this->menuItems));
            }
            DB::table('menu_item_addons')->insert([
                ['menu_item_id' => $this->menuItems[$saladIndex]->id, 'addon_id' => $this->addons[6]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Grilled Chicken
                ['menu_item_id' => $this->menuItems[$saladIndex]->id, 'addon_id' => $this->addons[2]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Avocado
            ]);
        }

        // Pasta addons (Spaghetti Carbonara)
        DB::table('menu_item_addons')->insert([
            ['menu_item_id' => $this->menuItems[5]->id, 'addon_id' => $this->addons[1]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Bacon
            ['menu_item_id' => $this->menuItems[5]->id, 'addon_id' => $this->addons[6]->id, 'max_quantity' => 1, 'is_required' => 0, 'created_at' => now(), 'updated_at' => now()], // Grilled Chicken
        ]);
    }

    /**
     * Seed combo meals
     */
    private function seedCombos(): void
    {
        // Get variant IDs for combos
        $pepperoniMedium = MenuVariant::where('menu_item_id', $this->menuItems[8]->id)->where('name', 'Medium (12")')->first();
        $margheritaLarge = MenuVariant::where('menu_item_id', $this->menuItems[7]->id)->where('name', 'Large (14")')->first();
        $wings10pc = MenuVariant::where('menu_item_id', $this->menuItems[0]->id)->where('name', '10 Pieces')->first();
        $ojMedium = MenuVariant::where('menu_item_id', $this->menuItems[16]->id)->where('name', 'Medium (16oz)')->first();
        $coffeeMedium = MenuVariant::where('menu_item_id', $this->menuItems[17]->id)->where('name', 'Medium (16oz)')->first();
        $smoothieRegular = MenuVariant::where('menu_item_id', $this->menuItems[18]->id)->where('name', 'Regular')->first();

        // Combo 1: Pizza & Wings Deal
        $combo1 = Combo::create([
            'name' => 'Pizza & Wings Combo',
            'slug' => 'pizza-wings-combo',
            'description' => 'Perfect combo for sharing! Get a medium pepperoni pizza and 10 pieces of chicken wings.',
            'combo_price' => 24.99,
            'original_price' => 28.98,
            'discount_type' => 'fixed',
            'discount_value' => 3.99,
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(60),
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo1->id, 'menu_item_id' => $this->menuItems[8]->id, 'variant_id' => $pepperoniMedium->id, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo1->id, 'menu_item_id' => $this->menuItems[0]->id, 'variant_id' => $wings10pc->id, 'quantity' => 1]);

        // Combo 2: Burger Meal Deal
        $combo2 = Combo::create([
            'name' => 'Classic Burger Meal',
            'slug' => 'classic-burger-meal',
            'description' => 'Classic beef burger with a refreshing beverage. Perfect lunch combo!',
            'combo_price' => 17.99,
            'original_price' => 19.98,
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(90),
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo2->id, 'menu_item_id' => $this->menuItems[10]->id, 'variant_id' => null, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo2->id, 'menu_item_id' => $this->menuItems[16]->id, 'variant_id' => $ojMedium->id, 'quantity' => 1]);

        // Combo 3: Family Pizza Feast
        $combo3 = Combo::create([
            'name' => 'Family Pizza Feast',
            'slug' => 'family-pizza-feast',
            'description' => 'Two large pizzas perfect for the whole family!',
            'combo_price' => 39.99,
            'original_price' => 47.98,
            'discount_type' => 'fixed',
            'discount_value' => 7.99,
            'start_date' => now()->subDays(10),
            'end_date' => null,
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo3->id, 'menu_item_id' => $this->menuItems[8]->id, 'variant_id' => $margheritaLarge->id, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo3->id, 'menu_item_id' => $this->menuItems[7]->id, 'variant_id' => $margheritaLarge->id, 'quantity' => 1]);

        // Combo 4: Pasta & Salad Combo
        $combo4 = Combo::create([
            'name' => 'Italian Pasta & Salad',
            'slug' => 'italian-pasta-salad',
            'description' => 'Delicious spaghetti carbonara paired with a fresh Caesar salad.',
            'combo_price' => 24.99,
            'original_price' => 27.98,
            'discount_type' => 'fixed',
            'discount_value' => 2.99,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(45),
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo4->id, 'menu_item_id' => $this->menuItems[5]->id, 'variant_id' => null, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo4->id, 'menu_item_id' => $this->menuItems[19]->id, 'variant_id' => null, 'quantity' => 1]);

        // Combo 5: Dessert & Coffee Combo
        $combo5 = Combo::create([
            'name' => 'Sweet Afternoon Break',
            'slug' => 'sweet-afternoon-break',
            'description' => 'Indulge in our chocolate lava cake with a refreshing iced coffee.',
            'combo_price' => 10.99,
            'original_price' => 12.48,
            'discount_type' => 'percentage',
            'discount_value' => 12,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo5->id, 'menu_item_id' => $this->menuItems[13]->id, 'variant_id' => null, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo5->id, 'menu_item_id' => $this->menuItems[17]->id, 'variant_id' => $coffeeMedium->id, 'quantity' => 1]);

        // Combo 6: Healthy Choice Combo
        $combo6 = Combo::create([
            'name' => 'Healthy Choice Combo',
            'slug' => 'healthy-choice-combo',
            'description' => 'Grilled salmon with Greek salad and a tropical smoothie for a nutritious meal.',
            'combo_price' => 38.99,
            'original_price' => 43.97,
            'discount_type' => 'fixed',
            'discount_value' => 4.98,
            'start_date' => now()->subDays(20),
            'end_date' => now()->addDays(60),
            'is_active' => 1,
            'status' => 1,
        ]);
        ComboItem::create(['combo_id' => $combo6->id, 'menu_item_id' => $this->menuItems[3]->id, 'variant_id' => null, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo6->id, 'menu_item_id' => $this->menuItems[20]->id, 'variant_id' => null, 'quantity' => 1]);
        ComboItem::create(['combo_id' => $combo6->id, 'menu_item_id' => $this->menuItems[18]->id, 'variant_id' => $smoothieRegular->id, 'quantity' => 1]);
    }
}
