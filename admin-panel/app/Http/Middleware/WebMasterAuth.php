<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebMasterAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('webmaster')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            return redirect()->route('webmaster.login');
        }

        $webmaster = auth('webmaster')->user();
        
        if (!$webmaster->is_active) {
            auth('webmaster')->logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Compte inactif'], 403);
            }
            return redirect()->route('webmaster.login')->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        return $next($request);
    }
}
