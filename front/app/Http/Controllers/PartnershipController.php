<?php

namespace App\Http\Controllers;

use App\Models\Partnership;
use App\Services\PartnershipService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    protected PartnershipService $partnershipService;

    public function __construct(PartnershipService $partnershipService)
    {
        $this->partnershipService = $partnershipService;
    }

    /**
     * Afficher la page de partenariat
     */
    public function index()
    {
        return $this->partnershipService->index();
    }

    /**
     * Traiter la soumission du formulaire de partenariat
     */
    public function store(Request $request, $locale)
    {
        return $this->partnershipService->store($request, $locale);
    }

    /**
     * Afficher la liste des partenariats (pour l'admin)
     */
    public function admin()
    {
        return $this->partnershipService->admin();
    }

    /**
     * Mettre à jour le statut d'un partenariat
     */
    public function updateStatus(Request $request, Partnership $partnership)
    {
        return $this->partnershipService->updateStatus($request, $partnership);
    }
}
