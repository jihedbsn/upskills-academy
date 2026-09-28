<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetEnglishLocale
{
    public function handle(Request $request, Closure $next)
    {
        App::setLocale('en');
        return $next($request);
    }
}


