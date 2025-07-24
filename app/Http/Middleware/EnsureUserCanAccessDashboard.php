<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user can access dashboard (not regular user)
        if (!Auth::user()->canAccessDashboard()) {
            // Redirect regular users to the home page with a message
            return redirect()->route('home')->with('error', 'You do not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
