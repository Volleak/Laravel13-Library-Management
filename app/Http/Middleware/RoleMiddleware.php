<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Check if the user has ANY of the allowed roles for this route
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request); // Allowed!
            }
        }

        // 3. If they don't have the role, block them
        abort(403, 'Unauthorized access. You do not have the correct role to view this page.');
    }
}