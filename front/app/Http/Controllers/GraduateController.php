<?php

namespace App\Http\Controllers;

use App\Services\GraduateService;

class GraduateController extends Controller
{
    protected GraduateService $graduateService;

    public function __construct(GraduateService $graduateService)
    {
        $this->graduateService = $graduateService;
    }

    /**
     * API endpoint to return complete JSON data
     */
    public function apiData()
    {
        return $this->graduateService->apiData();
    }

    /**
     * Display the graduates page grouped by year
     */
    public function index($locale)
    {
        return $this->graduateService->index($locale);
    }

    /**
     * Display graduates for a specific year
     */
    public function year($locale, $year)
    {
        return $this->graduateService->year($locale, $year);
    }

    /**
     * Display a specific certificate by serial number
     */
    public function certificate($locale, $serialnumber)
    {
        return $this->graduateService->certificate($locale, $serialnumber);
    }
}

