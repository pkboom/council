<?php

namespace App\Http\Middleware;

use Closure;

class Administrator
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isAdmin) {
            return $next($request);
        }

        abort(403, 'You do not have permission to perform this action');
    }
}
