<?php

namespace App\Http\Controllers;

use App\Services\HomeService;

class HomeController extends Controller
{
    protected HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        return $this->homeService->index();
    }

    /**
     * About page
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Privacy policy
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Terms of service
     */
    public function terms()
    {
        return view('pages.terms');
    }
}
