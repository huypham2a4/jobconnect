<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = ['role', 'permission', 'is_allowed'];

    protected function casts(): array
    {
        return ['is_allowed' => 'boolean'];
    }

    /** @return array<int, string> */
    public static function availableKeys(string $role): array
    {
        $keys = [];
        foreach (config('role_permissions.resources') as $resource => $definition) {
            if (in_array($role, $definition['roles'], true)) {
                foreach ($definition['actions'] as $action) {
                    $keys[] = $resource.'.'.$action;
                }
            }
        }

        return $keys;
    }

    /** @return array<string, bool> */
    public static function forRole(string $role): array
    {
        $stored = self::where('role', $role)->pluck('is_allowed', 'permission');
        $required = config('role_permissions.required.'.$role, []);
        $permissions = [];
        foreach (self::availableKeys($role) as $key) {
            $permissions[$key] = in_array($key, $required, true) || (bool) ($stored[$key] ?? false);
        }

        return $permissions;
    }

    public static function allows(string $role, string $permission): bool
    {
        return self::forRole($role)[$permission] ?? false;
    }
}
