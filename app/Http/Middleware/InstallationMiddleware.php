<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If installation is already completed, redirect to home
        if (env('INSTALLATION_COMPLETED', false)) {
            return redirect('/')->with('error', 'Installation already completed.');
        }

        return $next($request);
    }
}
