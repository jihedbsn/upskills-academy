<?php

namespace App\Support;

/**
 * Build absolute URLs on the student (user) app with exactly one locale segment.
 * USER_URL may be configured as https://domain or https://domain/en — strip trailing /en|ar|fr once.
 */
class StudentFrontendUrl
{
    public static function localized(string $locale, string $path = ''): string
    {
        $locale = in_array($locale, ['en', 'ar', 'fr'], true) ? $locale : 'en';
        $base = rtrim(config('app.user_url', env('USER_URL', 'http://localhost:8000')), '/');
        $base = preg_replace('#/(en|ar|fr)$#', '', $base);
        $path = ltrim($path, '/');

        return $path !== '' ? "{$base}/{$locale}/{$path}" : "{$base}/{$locale}";
    }
}
