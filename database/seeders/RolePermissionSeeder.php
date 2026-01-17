<?php

namespace Database\Seeders;

use App\Traits\PermissionsTrait;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    use PermissionsTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        Permission::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $roleSuperAdmin = Role::updateOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);

        $permissions = self::getSuperAdminPermissions();

        for ($i = 0; $i < count($permissions); $i++) {
            $permissionGroup = $permissions[$i]['group_name'];

            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                $permission = Permission::updateOrCreate([
                    'name' => $permissions[$i]['permissions'][$j],
                    'group_name' => $permissionGroup,
                    'guard_name' => 'admin',
                ]);

                $roleSuperAdmin->givePermissionTo($permission);
            }
        }

        // Create Waiter Role with specific permissions
        $this->createWaiterRole();

        // Create Kitchen Staff Role
        $this->createKitchenStaffRole();

        // Create Cashier Role
        $this->createCashierRole();
    }

    /**
     * Create Waiter role with limited permissions
     */
    private function createWaiterRole(): void
    {
        $roleWaiter = Role::updateOrCreate(['name' => 'Waiter', 'guard_name' => 'admin']);

        // Waiter specific permissions
        $waiterPermissions = [
            // Waiter Dashboard & Order Management
            'waiter.dashboard',
            'waiter.table.view',
            'waiter.order.create',
            'waiter.order.view',
            'waiter.order.update',
            'waiter.order.cancel',
            // Table viewing (read-only)
            'table.view',
            // Menu viewing for taking orders
            'menu.category.view',
            'menu.item.view',
            'menu.addon.view',
            'menu.combo.view',
            // Split bill
            'split.view',
            'split.create',
            // Void (limited)
            'void.item',
        ];

        foreach ($waiterPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleWaiter->givePermissionTo($permission);
            }
        }
    }

    /**
     * Create Kitchen Staff role with kitchen display permissions
     */
    private function createKitchenStaffRole(): void
    {
        $roleKitchen = Role::updateOrCreate(['name' => 'Kitchen Staff', 'guard_name' => 'admin']);

        // Kitchen Staff specific permissions
        $kitchenPermissions = [
            // Kitchen Display
            'kitchen.view',
            'kitchen.update_status',
            'kitchen.bump_order',
            'kitchen.view_history',
            // Menu viewing (to know what to prepare)
            'menu.item.view',
            'menu.addon.view',
        ];

        foreach ($kitchenPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleKitchen->givePermissionTo($permission);
            }
        }
    }

    /**
     * Create Cashier role with POS and payment permissions
     */
    private function createCashierRole(): void
    {
        $roleCashier = Role::updateOrCreate(['name' => 'Cashier', 'guard_name' => 'admin']);

        // Cashier specific permissions
        $cashierPermissions = [
            // Dashboard
            'dashboard.view',
            // POS
            'pos.view',
            'pos.running_orders.view',
            'pos.running_orders.update',
            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.invoice',
            // Customer (for quick customer creation)
            'customer.view',
            'customer.create',
            // Menu viewing
            'menu.category.view',
            'menu.item.view',
            'menu.addon.view',
            'menu.combo.view',
            // Tables
            'table.view',
            // Split bill
            'split.view',
            'split.create',
            'split.process_payment',
            // Void
            'void.item',
            'void.view_history',
            // Membership/Loyalty
            'membership.view',
            'membership.view_transactions',
            // Printer
            'printer.view',
        ];

        foreach ($cashierPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleCashier->givePermissionTo($permission);
            }
        }
    }
}
