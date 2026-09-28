<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from route parameter
        $locale = $request->route('locale');

        // If not in route, check session or default
        if (!$locale) {
            $locale = Session::get('locale', config('app.locale'));
        }

        // Validate locale
        if (!in_array($locale, ['en', 'ar', 'fr'])) {
            $locale = config('app.locale');
        }

        // Set the application locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Set default locale for URLs so we don't have to pass it to every route() call
        \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);

        // Log for debugging
        Log::info('SetLocale middleware - Current locale: ' . $locale);

        return $next($request);
    }
}
