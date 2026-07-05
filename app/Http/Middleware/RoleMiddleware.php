<?php

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->user() || !auth()->user()->hasRole($role)) {
            abort(403, 'Unauthorized access.');
        }
        return $next($request);

    }
}
