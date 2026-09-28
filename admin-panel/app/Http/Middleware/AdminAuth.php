<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exclure les routes d'authentification
        $excludedRoutes = [
            'login',
            'logout',
        ];

        if (in_array($request->route()->getName(), $excludedRoutes) ||
            $request->is('login') ||
            $request->is('logout')) {
            return $next($request);
        }

        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            return redirect()->route('login');
        }

        $admin = auth()->user();

        if (!$admin->is_active) {
            auth()->logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Compte inactif'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        return $next($request);
    }
}
