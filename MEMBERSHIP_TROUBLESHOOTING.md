# Membership Module - Troubleshooting Guide

## 🔧 Common Issues & Solutions

### Issue 1: 404 Error on `/admin/membership/programs`

**Problem**: Routes not found at `/admin/membership/...`

**Cause**: Routes are registered at `/membership` not `/admin/membership`

**Solution**: Use correct URLs

```
❌ Wrong:  http://127.0.0.1:8000/admin/membership/programs
✅ Correct: http://127.0.0.1:8000/membership/programs
```

**Correct URLs**:
- Dashboard: `http://127.0.0.1:8000/membership`
- Programs: `http://127.0.0.1:8000/membership/programs`
- Rules: `http://127.0.0.1:8000/membership/rules`
- Customers: `http://127.0.0.1:8000/membership/customers`
- Transactions: `http://127.0.0.1:8000/membership/transactions`

---

### Issue 2: "Membership" Link Not Showing in Sidebar

**Problem**: Membership link is missing from navigation

**Cause**: One of the following:
1. Admin user doesn't have `membership.view` permission
2. Admin is not logged in with `admin` guard
3. Route cache is stale

**Solution - Step 1: Check Authentication**

Make sure you're logged in as an admin user:
```
Check URL should show: /admin/dashboard
Not: /dashboard
```

**Solution - Step 2: Clear Cache**

```bash
php artisan cache:clear
php artisan permission:cache-reset
php artisan view:clear
```

**Solution - Step 3: Verify Permissions**

```bash
php artisan tinker

# Check if admin has permission
> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view');

# Should return: true
```

**Solution - Step 4: Assign Permission if Missing**

```bash
php artisan tinker

> $admin = App\Models\Admin::find(1);
> $admin->givePermissionTo('membership.view');
```

**Solution - Step 5: Verify Routes are Loaded**

```bash
php artisan route:list | Select-String -Pattern "membership"
```

Should show routes like:
```
GET|HEAD  membership  .....................  membership.index
GET|HEAD  membership/programs  ...........  programs.index
```

---

### Issue 3: 403 Forbidden Error

**Problem**: Getting 403 error when trying to access routes

**Cause**: Admin user doesn't have required permission

**Solution**:

```bash
php artisan tinker

# Seed all permissions
> use Modules\Membership\Database\Seeders\MembershipPermissionsSeeder;
> (new MembershipPermissionsSeeder())->run();

# Assign role to admin
> $admin = App\Models\Admin::find(1);
> $admin->assignRole('super_admin');
```

---

### Issue 4: Sidebar Link Not Working / 404 After Click

**Problem**: Clicking "Membership" link gives 404 error

**Cause**: Routes not registered or permission issue

**Solution**:

1. **Verify Module is Enabled**
   ```bash
   # Check if Membership module is enabled
   php artisan module:list
   ```
   Should show: `Membership` with status `Enabled`

2. **If Disabled, Enable It**
   ```bash
   php artisan module:enable Membership
   ```

3. **Clear Caches**
   ```bash
   php artisan cache:clear
   php artisan permission:cache-reset
   php artisan config:cache
   ```

4. **Check Routes**
   ```bash
   php artisan route:list | Select-String -Pattern "membership"
   ```

---

### Issue 5: "Membership" Shows But Gives Error After Click

**Problem**: Link appears in sidebar but clicking gives error

**Cause**: Usually a missing view file or controller issue

**Solution**:

1. **Check View Exists**
   ```
   Modules/Membership/resources/views/dashboard.blade.php
   ```

2. **Check Controller Method**
   ```bash
   # Verify MembershipController has index() method
   php artisan tinker
   > $controller = new Modules\Membership\app\Http\Controllers\MembershipController();
   > method_exists($controller, 'index');
   ```

3. **If view missing, create it**
   - See: `Modules/Membership/resources/views/dashboard.blade.php`

---

### Issue 6: API Endpoints Not Working

**Problem**: API calls to `/api/v1/membership/*` returning 404 or 401

**Cause**: 
1. API routes not registered
2. Missing authentication token
3. API routes using wrong guard

**Solution**:

**For 404 Errors**:
```bash
# Check API routes exist
php artisan route:list | Select-String -Pattern "api.membership"
```

**For 401 Errors**:
```bash
# Get API token
php artisan tinker

> $admin = App\Models\Admin::first();
> $token = $admin->createToken('pos-terminal')->plainTextToken;

# Use in API calls
# Authorization: Bearer {token}
```

**Test API with cURL**:
```bash
curl -X POST http://127.0.0.1:8000/api/v1/membership/identify \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+1234567890"}'
```

---

### Issue 7: Permission Middleware Error

**Problem**: "Unauthorized" or permission denied errors

**Cause**: Routes require `permission:membership.view` but admin lacks it

**Solution**:

```bash
php artisan tinker

# Option 1: Give permission directly
> $admin = App\Models\Admin::find(1);
> $admin->givePermissionTo('membership.view', 'membership.create', 'membership.edit', 'membership.delete', 'membership.manage_points', 'membership.view_transactions');

# Option 2: Assign role (includes permissions)
> $admin->assignRole('super_admin');
```

---

### Issue 8: Module Commands Not Working

**Problem**: Cannot run membership seeder or module commands

**Cause**: Module not properly registered or enabled

**Solution**:

```bash
# List modules
php artisan module:list

# If Membership is Disabled, enable it
php artisan module:enable Membership

# Clear module cache
php artisan module:cache:clear

# Verify it's loaded
php artisan module:list
```

---

## ✅ Complete Setup Verification Checklist

Run through this checklist to verify everything is working:

```bash
# Step 1: Check module is enabled
php artisan module:list

# Step 2: Check routes exist
php artisan route:list | Select-String -Pattern "membership"

# Step 3: Check permissions exist
php artisan tinker
> use Spatie\Permission\Models\Permission;
> Permission::where('guard_name', 'admin')->pluck('name');

# Step 4: Check roles exist
> use Spatie\Permission\Models\Role;
> Role::where('guard_name', 'admin')->pluck('name');

# Step 5: Check admin has permission
> $admin = App\Models\Admin::find(1);
> $admin->hasPermissionTo('membership.view');

# Step 6: Check admin is logged in as 'admin' guard
> Auth::guard('admin')->check();

# Step 7: Exit tinker
> exit;

# Step 8: Test in browser
# Visit: http://127.0.0.1:8000/membership
# Should see dashboard or permission denied error
```

---

## 🚀 Quick Fix (Nuclear Option)

If everything is broken, run this complete reset:

```bash
# Clear all caches
php artisan cache:clear
php artisan permission:cache-reset
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migrate fresh (⚠️ This will reset database)
# php artisan migrate:fresh --seed

# Or just re-seed permissions
php artisan db:seed --class=Modules\\Membership\\Database\\Seeders\\MembershipDatabaseSeeder

# Re-enable module
php artisan module:enable Membership

# Assign permission to admin
php artisan tinker
> App\Models\Admin::find(1)->assignRole('super_admin');
> exit;
```

Then visit: `http://127.0.0.1:8000/membership`

---

## 🔍 Debug Checklist

When something isn't working:

- [ ] Is the module enabled? `php artisan module:list`
- [ ] Are routes registered? `php artisan route:list | grep membership`
- [ ] Does admin have permission? `php artisan tinker` → `$admin->hasPermissionTo('membership.view')`
- [ ] Is admin logged in? Check URL (should be /admin/)
- [ ] Are caches cleared? `php artisan cache:clear`
- [ ] Is view file present? `Modules/Membership/resources/views/dashboard.blade.php`
- [ ] Is controller method present? Check MembershipController@index exists
- [ ] Are migrations run? `php artisan migrate`

---

## 📞 Getting Help

For specific issues:

1. Check this troubleshooting guide
2. Run: `php artisan route:list | Select-String -Pattern "membership"`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Review documentation files in root directory

---

## ✨ Summary

**Common Issues Fixed**:
- ✅ Correct route URLs (`/membership` not `/admin/membership`)
- ✅ Admin guard authentication in sidebar
- ✅ Permission checks
- ✅ Module enabled/disabled
- ✅ Route caching
- ✅ API authentication

**Most Common Solution**: Run permissions seeder and clear cache
```bash
php artisan db:seed --class=Modules\\Membership\\Database\\Seeders\\MembershipDatabaseSeeder
php artisan cache:clear
```

**Then visit**: `http://127.0.0.1:8000/membership`
