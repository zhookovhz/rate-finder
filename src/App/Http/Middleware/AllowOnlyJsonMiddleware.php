<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class AllowOnlyJsonMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->expectsJson()) {
            return response()->json(['message' => 'Not Acceptable'], 406);
        }

        return $next($request);
    }
}
