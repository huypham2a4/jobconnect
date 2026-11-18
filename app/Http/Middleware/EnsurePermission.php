<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routes = config('role_permissions.routes');
        $keys = (array) ($routes[$request->route()->getName()] ?? []);
        $user = $request->user();
        $permissions = $user && $user->is_active ? RolePermission::forRole($user->role) : [];
        $allowed = collect($keys)->contains(fn (string $key): bool => $permissions[$key] ?? false);
        abort_unless($allowed, 403, 'Vai trò của bạn chưa được cấp quyền thực hiện chức năng này.');

        return $next($request);
    }
}
