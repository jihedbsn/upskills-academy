<?php

namespace App\Services;

use App\Models\Carousel;
use App\Models\AboutAcceuil;
use App\Models\AvisAcceuil;
use App\Models\ProductAcceuil;
use App\Models\NosPartenaires;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AccueilService
{
    private const HOMEPAGE_CACHE_KEY = 'accueil.index.payload';
    private const HOMEPAGE_CACHE_TTL_MINUTES = 5;

    public function handleIndex(Request $request)
    {
        if ($request->filled(['id', 'email'])) {
            $id = $request->query('id');
            $email = $request->query('email');

            $headerReferer = (string) $request->headers->get('referer');

            $isFromLocalhost = false;
            if ($headerReferer !== '') {
                $refererHost = parse_url($headerReferer, PHP_URL_HOST);
                $isFromLocalhost = in_array($refererHost, $this->getAllowedHosts(), true);
            }

            if (
                is_numeric($id)
                && filter_var($email, FILTER_VALIDATE_EMAIL)
                && $isFromLocalhost
            ) {
                $student = Student::where('id', (int) $id)
                    ->where('email', $email)
                    ->first();

                if ($student) {
                    if (!Auth::check() || Auth::id() !== $student->id) {
                        if (Auth::check()) {
                            Auth::logout();
                        }
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        Auth::login($student, true);
                        $request->session()->regenerate();
                        $request->session()->save();
                    }

                    return redirect()->route('student-dashboard.index');
                }
            }
        }

        $data = Cache::remember(
            self::HOMEPAGE_CACHE_KEY,
            now()->addMinutes(self::HOMEPAGE_CACHE_TTL_MINUTES),
            function () {
                return [
                    'carousels' => Carousel::active()
                        ->ordered()
                        ->get(),
                    'aboutAcceuil' => AboutAcceuil::active()->first(),
                    'avisAcceuil' => AvisAcceuil::active()
                        ->ordered()
                        ->get(),
                    'coursesAcceuil' => ProductAcceuil::active()
                        ->ordered()
                        ->with(['product.variations', 'product.teacher', 'product.category'])
                        ->get(),
                    'partenaires' => NosPartenaires::get(),
                    'achievements' => Achievement::getInstance(),
                ];
            }
        );

        return view('accueil.index', $data);
    }

    private function getAllowedHosts(): array
    {
        $adminUrl = (string) config('app.admin_url', env('ADMIN_URL', 'http://localhost:8001'));
        $adminDomain = parse_url($adminUrl, PHP_URL_HOST);

        $allowedHosts = ['localhost', '127.0.0.1', '::1'];

        if ($adminDomain) {
            $allowedHosts[] = $adminDomain;
        }

        return $allowedHosts;
    }
}
