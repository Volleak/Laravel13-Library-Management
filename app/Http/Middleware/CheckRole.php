<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Add Auth import

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Fixed: using Auth::check() instead of auth()check()
        if (!Auth::check()) {
            return redirect('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Fixed: using the typed $user variable
        if (!in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}