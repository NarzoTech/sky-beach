# Membership & Loyalty System - Permissions Setup Guide

## ✅ Permissions Implementation Complete

All necessary permissions have been created and can be automatically seeded into your application.

---

## 🔐 Permissions Created

### 6 Membership Permissions

| Permission | Description | Use Case |
|-----------|-------------|----------|
| `membership.view` | View membership programs and customers | Dashboard access, view data |
| `membership.create` | Create loyalty programs and rules | Create new programs/rules |
| `membership.edit` | Edit loyalty programs and rules | Modify existing programs/rules |
| `membership.delete` | Delete loyalty programs and rules | Remove programs/rules |
| `membership.manage_points` | Manually adjust customer points | Admin point adjustments |
| `membership.view_transactions` | View transaction audit logs | Access transaction history |

---

## 👥 Role Assignments

### Super Admin Role
- **Name**: `super_admin`
- **All Permissions**: ✅ All 6 permissions
- **Use Case**: System administrator with full control

### Admin Role
- **Name**: `admin`
- **Permissions**:
  - ✅ membership.view
  - ✅ membership.create
  - ✅ membership.edit
  - ✅ membership.manage_points
  - ✅ membership.view_transactions
  - ❌ membership.delete (restricted)
- **Use Case**: Manager can manage programs but can't delete

### Manager Role
- **Name**: `manager`
- **Permissions**:
  - ✅ membership.view
  - ✅ membership.view_transactions
  - ❌ membership.create
  - ❌ membership.edit
  - ❌ membership.delete
  - ❌ membership.manage_points
- **Use Case**: View-only access to loyalty data

---

## 🚀 Setup Instructions

### Option 1: Automatic Setup (Recommended)

#### Step 1: Run the Membership Seeder
```bash
php artisan db:seed --class=Modules\\Membership\\Database\\Seeders\\MembershipDatabaseSeeder
```

This will automatically:
- Create all 6 permissions
- Create 3 roles (super_admin, admin, manager)
- Assign permissions to each role

#### Step 2: Assign Roles to Admin Users
```bash
php artisan tinker

# Assign super admin role
> $admin = App\Models\Admin::find(1);
> $admin->assignRole('super_admin');

# Or assign regular admin role
> $admin->assignRole('admin');

# Or assign manager role
> $admin->assignRole('manager');
```

---

### Option 2: Manual Setup

#### Step 1: Create Permissions
```bash
php artisan tinker

> use Spatie\Permission\Models\Permission;
> Permission::create(['name' => 'membership.view', 'guard_name' => 'admin', 'description' => 'View membership programs and customers']);
> Permission::create(['name' => 'membership.create', 'guard_name' => 'admin', 'description' => 'Create loyalty programs and rules']);
> Permission::create(['name' => 'membership.edit', 'guard_name' => 'admin', 'description' => 'Edit loyalty programs and rules']);
> Permission::create(['name' => 'membership.delete', 'guard_name' => 'admin', 'description' => 'Delete loyalty programs and rules']);
> Permission::create(['name' => 'membership.manage_points', 'guard_name' => 'admin', 'description' => 'Manually adjust customer points']);
> Permission::create(['name' => 'membership.view_transactions', 'guard_name' => 'admin', 'description' => 'View transaction audit logs']);
```

#### Step 2: Create Roles
```bash
php artisan tinker

> use Spatie\Permission\Models\Role;
> Role::create(['name' => 'super_admin', 'guard_name' => 'admin', 'description' => 'Super Administrator with all permissions']);
> Role::create(['name' => 'admin', 'guard_name' => 'admin', 'description' => 'Administrator']);
> Role::create(['name' => 'manager', 'guard_name' => 'admin', 'description' => 'Manager']);
```

#### Step 3: Assign Permissions to Roles
```bash
php artisan tinker

> use Spatie\Permission\Models\Role;
> 
> $superAdmin = Role::findByName('super_admin', 'admin');
> $superAdmin->syncPermissions(['membership.view', 'membership.create', 'membership.edit', 'membership.delete', 'membership.manage_points', 'membership.view_transactions']);
>
> $admin = Role::findByName('admin', 'admin');
> $admin->syncPermissions(['membership.view', 'membership.create', 'membership.edit', 'membership.manage_points', 'membership.view_transactions']);
>
> $manager = Role::findByName('manager', 'admin');
> $manager->syncPermissions(['membership.view', 'membership.view_transactions']);
```

#### Step 4: Assign Roles to Admin Users
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->assignRole('super_admin');
```

---

## ✅ Verification

### Check Permissions Were Created
```bash
php artisan tinker

> use Spatie\Permission\Models\Permission;
> Permission::where('guard_name', 'admin')->pluck('name');
```

Expected output:
```
[
  "membership.view",
  "membership.create",
  "membership.edit",
  "membership.delete",
  "membership.manage_points",
  "membership.view_transactions"
]
```

### Check Roles Were Created
```bash
php artisan tinker

> use Spatie\Permission\Models\Role;
> Role::where('guard_name', 'admin')->pluck('name');
```

Expected output:
```
[
  "super_admin",
  "admin",
  "manager"
]
```

### Check Admin Has Permission
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view');
```

Expected output: `true`

### Check Admin Can Access Route
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->can('membership.view');
```

Expected output: `true`

---

## 🔍 How Permissions Work in the System

### In Routes (web.php)
```php
Route::middleware(['auth:admin', 'permission:membership.view'])->group(function () {
    Route::get('membership', [MembershipController::class, 'index'])->name('membership.index');
});
```

### In Controllers
```php
public function __construct()
{
    $this->middleware('permission:membership.create')->only(['create', 'store']);
    $this->middleware('permission:membership.edit')->only(['edit', 'update']);
    $this->middleware('permission:membership.delete')->only(['destroy']);
}
```

### In Views
```blade
@can('membership.view')
    <a href="{{ route('membership.index') }}">Membership</a>
@endcan
```

### In Sidebar Navigation
```blade
@can('membership.view')
<x-nav-link :href="route('membership.index')" :active="request()->routeIs('membership.*')">
    {{ __('Membership') }}
</x-nav-link>
@endcan
```

---

## 📋 Permission Hierarchy

```
Super Admin (All)
├── membership.view ✅
├── membership.create ✅
├── membership.edit ✅
├── membership.delete ✅
├── membership.manage_points ✅
└── membership.view_transactions ✅

Admin (No Delete)
├── membership.view ✅
├── membership.create ✅
├── membership.edit ✅
├── membership.manage_points ✅
├── membership.view_transactions ✅
└── membership.delete ❌

Manager (View Only)
├── membership.view ✅
├── membership.view_transactions ✅
├── membership.create ❌
├── membership.edit ❌
├── membership.delete ❌
└── membership.manage_points ❌
```

---

## 🛠️ Managing Permissions

### Add Permission to Role
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->givePermissionTo('membership.create');
```

### Remove Permission from Role
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->revokePermissionTo('membership.delete');
```

### Check if Admin Has Permission
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view'); // true or false
```

### List All Admin Permissions
```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->getAllPermissions()->pluck('name');
```

---

## 🔐 Security Best Practices

1. **Principle of Least Privilege**: Assign only necessary permissions
   - Managers: view-only
   - Admins: create/edit
   - Super Admins: all permissions

2. **Regular Audits**: Review who has which permissions
   ```bash
   php artisan tinker
   > App\Models\Admin::with('roles', 'permissions')->get();
   ```

3. **Restrict Delete**: Only super admins can delete programs/rules

4. **Audit Trail**: All operations logged in transaction logs

5. **Password Protection**: Ensure admin accounts have strong passwords

---

## 📊 Permission Enforcement Points

| Feature | Permission Required | Location |
|---------|-------------------|----------|
| View Dashboard | `membership.view` | Route + Sidebar |
| View Programs | `membership.view` | Route |
| Create Program | `membership.create` | Route + Controller |
| Edit Program | `membership.edit` | Route + Controller |
| Delete Program | `membership.delete` | Route + Controller |
| View Customers | `membership.view` | Route |
| Adjust Points | `membership.manage_points` | Route + Controller |
| View Transactions | `membership.view_transactions` | Route |
| Access API | `membership.view` | API Auth |

---

## 🚀 Quick Start

### Fastest Setup (3 commands)
```bash
# 1. Run migrations
php artisan migrate

# 2. Seed permissions and roles
php artisan db:seed --class=Modules\\Membership\\Database\\Seeders\\MembershipDatabaseSeeder

# 3. Assign role to admin
php artisan tinker
> App\Models\Admin::find(1)->assignRole('super_admin');
> exit;

# Done! Access via /membership
```

---

## ✅ Checklist

- [ ] Migrations run
- [ ] Permissions seeded
- [ ] Roles created
- [ ] Roles assigned to admins
- [ ] Test access to `/membership`
- [ ] Verify sidebar link appears
- [ ] Test all features
- [ ] Verify permissions are enforced

---

## 🆘 Troubleshooting

### Permission Link Not Showing in Sidebar
**Check**:
1. Admin user has `membership.view` permission
2. User has required role assigned
3. Admin is authenticated as guard `admin`

```bash
php artisan tinker
> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view');
```

### Can't Access /membership Route
**Check**:
1. Admin authenticated
2. User has `membership.view` permission
3. Guard is `admin`

```bash
php artisan tinker
> $admin = App\Models\Admin::find(1);
> $admin->hasRole('admin') || $admin->hasRole('super_admin');
```

### Permissions Not Taking Effect
**Solution**:
Clear permission cache:
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

---

## 📞 Support

For permission issues, check:
1. MEMBERSHIP_QUICK_START.md
2. MEMBERSHIP_IMPLEMENTATION_COMPLETE.md
3. Spatie Laravel Permissions documentation

---

## ✨ Summary

✅ **6 Permissions Created** - View, Create, Edit, Delete, Manage Points, View Transactions  
✅ **3 Roles Configured** - Super Admin, Admin, Manager  
✅ **Automatic Seeder** - Run one command to set up everything  
✅ **Permission Enforcement** - Protected routes and views  
✅ **Easy Management** - Artisan commands and tinker  

**Permissions Setup: COMPLETE & READY ✅**

Run the seeder to activate permissions immediately!
