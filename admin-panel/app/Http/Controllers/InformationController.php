<?php

namespace App\Http\Controllers;

use App\Services\InformationService;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    protected InformationService $informationService;

    public function __construct(InformationService $informationService)
    {
        $this->informationService = $informationService;
    }

    /**
     * Display the site information (edit form).
     */
    public function index()
    {
        return $this->informationService->index();
    }

    /**
     * Update the site information.
     */
    public function update(Request $request)
    {
        return $this->informationService->update($request);
    }
}
