<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'forget_password_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function getImageUrlAttribute()
    {;
        $setting = cache('setting');
        $value = $this->attributes['image'];

        // check if file is exists
        if ($value && !file_exists(public_path($value))) {
            if (str_contains($value, 'https:/')) {
                $value = $value;
            } else {
                $value = $this->media?->path;
                if ($value) {
                    $value = asset($value);
                }
            }
        } else if ($value) {
            $value = asset($value);
        }
        return $value ? $value : asset($setting->default_avatar);
    }
    public static function getPermissionGroup()
    {
        $permission_group = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();

        return $permission_group;
    }

    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();

        return $permissions;
    }

    public function scopeNotSuperAdmin($query)
    {
        return $query->where('is_super_admin', 0);
    }

    /**
     * Get the employee record linked to this admin
     */
    public function employee()
    {
        return $this->hasOne(\Modules\Employee\app\Models\Employee::class, 'admin_id');
    }

    /**
     * Check if admin is a waiter
     */
    public function isWaiter(): bool
    {
        return $this->employee && $this->employee->is_waiter;
    }

    public static function roleHasPermission($role, $permissions)
    {
        $hasPermission = true;

        // Ensure $permissions is a collection or an array
        foreach ($permissions as $permission) {
            // Check if the permission is an object and has a 'name' property
            if (is_object($permission) && isset($permission->name)) {
                // If role does not have the permission, return false early
                if (!$role->hasPermissionTo($permission->name)) {
                    return false;
                }
            } else {
                // Handle the case where $permission is not an object or 'name' doesn't exist
                $hasPermission = false;
                return $hasPermission;
            }
        }

        return $hasPermission; // Return true if all permissions exist
    }
}
