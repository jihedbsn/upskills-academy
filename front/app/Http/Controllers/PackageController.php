<?php

namespace App\Http\Controllers;

use App\Services\PackageFrontendService;

class PackageController extends Controller
{
    protected PackageFrontendService $packageFrontendService;

    public function __construct(PackageFrontendService $packageFrontendService)
    {
        $this->packageFrontendService = $packageFrontendService;
    }

    /**
     * Afficher la page des packages
     */
    public function index()
    {
        return $this->packageFrontendService->index();
    }

    /**
     * Afficher les détails d'un package
     */
    public function show($locale, $id)
    {
        return $this->packageFrontendService->show($locale, $id);
    }
}
