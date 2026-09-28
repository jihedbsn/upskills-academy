<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LanguageService
{
    public function changeLanguage(Request $request, $locale)
    {
        $allowedLocales = ['en', 'fr', 'ar'];

        if (!in_array($locale, $allowedLocales)) {
            $locale = 'en';
        }

        Session::put('locale', $locale);

        Log::info('Language changed to: ' . $locale);
        Log::info('Session locale: ' . Session::get('locale'));

        return redirect()->back();
    }
}
