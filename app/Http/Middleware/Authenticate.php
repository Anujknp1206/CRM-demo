<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user(); // Change to Auth::guard('admin')->user() if using an admin guard.

        // Check if user is not logged in
        if (!$user) {
            toast('Unauthorized access. You are not a valid user.', 'error');
            return redirect()->route('login');
        }

        // Check if user is disabled (status = 0)
        if ($user->is_active == false) {
            Auth::logout(); // Logout the user
            toast('Your account has been disabled. Contact admin.', 'error');
            return redirect()->route('login');
        }


        $response = $next($request);

        // Prevent browser from caching authenticated pages
        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', Carbon::now()->subYear()->toRfc7231String());
    }
}

