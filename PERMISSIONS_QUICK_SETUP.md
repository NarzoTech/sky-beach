# Membership Permissions - Quick Setup

## ⚡ One-Command Setup

### Run this single command:
```bash
php artisan db:seed --class=Modules\\Membership\\Database\\Seeders\\MembershipDatabaseSeeder
```

This automatically creates:
- ✅ 6 permissions
- ✅ 3 roles (super_admin, admin, manager)
- ✅ Permission-role assignments

---

## 👤 Assign to Admin User

### Option A: Super Admin (Full Access)
```bash
php artisan tinker
> App\Models\Admin::find(1)->assignRole('super_admin');
> exit;
```

### Option B: Admin (No Delete)
```bash
php artisan tinker
> App\Models\Admin::find(1)->assignRole('admin');
> exit;
```

### Option C: Manager (View Only)
```bash
php artisan tinker
> App\Models\Admin::find(1)->assignRole('manager');
> exit;
```

---

## ✅ Verify Setup

```bash
php artisan tinker

# Check permissions exist
> use Spatie\Permission\Models\Permission;
> Permission::where('guard_name', 'admin')->pluck('name');

# Check roles exist
> use Spatie\Permission\Models\Role;
> Role::where('guard_name', 'admin')->pluck('name');

# Check admin has access
> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view');

> exit;
```

---

## 🎯 That's It!

Your admin user now has membership permissions and can:
- ✅ See "Membership" in sidebar
- ✅ Access `/membership` dashboard
- ✅ Create/edit programs and rules
- ✅ Manage customers and points
- ✅ View transaction logs

---

## 📋 Permissions Reference

| Permission | Access |
|-----------|--------|
| `membership.view` | View dashboard & data |
| `membership.create` | Create programs/rules |
| `membership.edit` | Edit programs/rules |
| `membership.delete` | Delete programs/rules |
| `membership.manage_points` | Adjust points |
| `membership.view_transactions` | View audit logs |

---

## 🔐 Role Permissions

**Super Admin**: All permissions  
**Admin**: All except delete  
**Manager**: View only  

---

For detailed setup, see: MEMBERSHIP_PERMISSIONS_SETUP.md
