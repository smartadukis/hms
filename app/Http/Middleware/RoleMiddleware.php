<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth()->user();

        if ($user && ($user->role === $role || $user->role === 'admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
