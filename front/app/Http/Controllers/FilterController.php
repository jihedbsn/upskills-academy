<?php

namespace App\Http\Controllers;

use App\Services\FilterCatalogService;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    protected FilterCatalogService $filterCatalogService;

    public function __construct(FilterCatalogService $filterCatalogService)
    {
        $this->filterCatalogService = $filterCatalogService;
    }

    /**
     * Afficher la page de filtres des cours
     */
    public function index(Request $request)
    {
        return $this->filterCatalogService->index($request);
    }

    /**
     * Afficher les détails d'un cours
     */
    public function show($locale, $product)
    {
        return $this->filterCatalogService->show((string) $locale, $product);
    }
}
