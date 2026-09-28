<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrWebMasterAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si un admin ou un webmaster est connecté
        if (auth('admin')->check() || auth('webmaster')->check()) {
            $user = auth('admin')->check() ? auth('admin')->user() : auth('webmaster')->user();
            
            // Vérifier si le compte est actif
            if (!$user->is_active) {
                auth('admin')->logout();
                auth('webmaster')->logout();
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Compte inactif'], 403);
                }
                return redirect()->route('admin.login')->withErrors(['email' => 'Votre compte a été désactivé.']);
            }
            
            return $next($request);
        }

        // Aucun utilisateur connecté
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }
        return redirect()->route('admin.login');
    }
}
