<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DemoResetMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /*
        |----------------------------------------------------
        | Don't block login page or assets
        |----------------------------------------------------
        */

        if (
            $request->is('login') ||
            $request->is('refresh-captcha') ||
            $request->is('admin/uploads/*') ||
            $request->is('admin/plugins/*') ||
            $request->is('admin/dist/*')
        ) {
            return $next($request);
        }

        /*
        |----------------------------------------------------
        | Demo Reset Mode
        |----------------------------------------------------
        */

        if (Cache::get('demo_resetting')) {

            return response()->view('demo-reset');

        }

        return $next($request);
    }
}