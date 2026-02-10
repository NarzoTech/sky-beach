<?php
namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Employee\app\Models\Employee;
use Spatie\Permission\Models\Role;

class AdminInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        if (! Admin::where('email', 'admin@gmail.com')->first()) {
            $admin           = new Admin();
            $admin->name     = 'John Doe';
            $admin->email    = 'admin@gmail.com';
            $admin->image    = 'uploads/website-images/admin.jpg';
            $admin->password = Hash::make(1234);
            $admin->status   = 'active';
            $admin->save();

            $role = Role::first();
            $admin?->assignRole($role);

            // Create employee record for admin
            Employee::create([
                'name'        => $admin->name,
                'email'       => $admin->email,
                'mobile'      => '1234567890',
                'designation' => 'Administrator',
                'address'     => 'Main Office',
                'join_date'   => now(),
                'salary'      => 0,
                'status'      => 'active',
                'admin_id'    => $admin->id,
                'is_waiter'   => false,
            ]);
        }

        // Create Waiter User
        if (! Admin::where('email', 'waiter@gmail.com')->first()) {
            $waiter           = new Admin();
            $waiter->name     = 'Demo Waiter';
            $waiter->email    = 'waiter@gmail.com';
            $waiter->image    = 'uploads/website-images/admin.jpg';
            $waiter->password = Hash::make(1234);
            $waiter->status   = 'active';
            $waiter->save();

            $waiterRole = Role::where('name', 'Waiter')->first();
            if ($waiterRole) {
                $waiter->assignRole($waiterRole);
            }

            // Create employee record for waiter
            Employee::create([
                'name'        => $waiter->name,
                'email'       => $waiter->email,
                'mobile'      => '1234567891',
                'designation' => 'Waiter',
                'address'     => 'Restaurant Floor',
                'join_date'   => now(),
                'salary'      => 15000,
                'status'      => 'active',
                'admin_id'    => $waiter->id,
                'is_waiter'   => true,
                'pin_code'    => '1234',
            ]);
        }

        // Create Cashier User
        if (! Admin::where('email', 'cashier@gmail.com')->first()) {
            $cashier           = new Admin();
            $cashier->name     = 'Demo Cashier';
            $cashier->email    = 'cashier@gmail.com';
            $cashier->image    = 'uploads/website-images/admin.jpg';
            $cashier->password = Hash::make(1234);
            $cashier->status   = 'active';
            $cashier->save();

            $cashierRole = Role::where('name', 'Cashier')->first();
            if ($cashierRole) {
                $cashier->assignRole($cashierRole);
            }

            // Create employee record for cashier
            Employee::create([
                'name'        => $cashier->name,
                'email'       => $cashier->email,
                'mobile'      => '1234567893',
                'designation' => 'Cashier',
                'address'     => 'Cash Counter',
                'join_date'   => now(),
                'salary'      => 10000,
                'status'      => 'active',
                'admin_id'    => $cashier->id,
                'is_waiter'   => false,
            ]);
        }

        // Link existing admins to employee records if not already linked
        $this->linkExistingAdminsToEmployees();
    }

    /**
     * Link existing admin users to employee records
     */
    private function linkExistingAdminsToEmployees(): void
    {
        $admins = Admin::whereDoesntHave('employee')->get();

        foreach ($admins as $admin) {
            // Check if admin has waiter role
            $isWaiter = $admin->hasRole('Waiter');

            Employee::create([
                'name'        => $admin->name,
                'email'       => $admin->email,
                'mobile'      => '',
                'designation' => $isWaiter ? 'Waiter' : 'Staff',
                'address'     => '',
                'join_date'   => $admin->created_at ?? now(),
                'salary'      => 0,
                'status'      => 'active',
                'admin_id'    => $admin->id,
                'is_waiter'   => $isWaiter,
            ]);
        }
    }
}
