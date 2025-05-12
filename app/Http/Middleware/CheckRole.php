<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        \Log::info('CheckRole middleware', [
            'user' => $request->user()->id ?? 'no-user',
            'role' => $role,
            'user_role' => $request->user()->role ?? 'no-role'
        ]);
        
        if (!$request->user() || !$request->user()->hasRole($role)) {
            abort(403, 'No tienes permiso para acceder a esta área.');
        }

        return $next($request);
    }
}
