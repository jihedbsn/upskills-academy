<?php

namespace App\Http\Controllers;

use App\Services\PageFrontendService;

class PageController extends Controller
{
    protected PageFrontendService $pageFrontendService;

    public function __construct(PageFrontendService $pageFrontendService)
    {
        $this->pageFrontendService = $pageFrontendService;
    }

    /**
     * Afficher une page spécifique
     */
    public function show($locale, $slug)
    {
        return $this->pageFrontendService->show($locale, $slug);
    }

    /**
     * Afficher toutes les pages (pour la section Why Academy)
     */
    public function index()
    {
        return $this->pageFrontendService->index();
    }

    /**
     * Obtenir les pages pour le menu (méthode utilitaire)
     */
    public static function getMenuPages()
    {
        return app(PageFrontendService::class)->getMenuPages();
    }
}
