<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || (! $user->isAdmin() && ! $user->isSuperAdmin())) {
            abort(403, 'Unauthorized — admin only');
        }

        return $next($request);
    }
}
