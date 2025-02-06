<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolePermissionUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $userRoles = $user->roles->pluck('name')->toArray();
            session(['user_role' => $userRoles]);

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            session(['user_permisos' => $userPermissions]);
        } else {
            session(['user_role' => []]);
            session(['user_permisos' => []]);
        }

        return $next($request);
    }

}
