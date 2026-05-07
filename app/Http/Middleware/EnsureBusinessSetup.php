<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureBusinessSetup
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if (!auth()->user()->business_id) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Business not configured.'], 422)
                : redirect()->route('login');
        }

        return $next($request);
    }
}
