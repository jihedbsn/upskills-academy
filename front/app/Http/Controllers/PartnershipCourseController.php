<?php

namespace App\Http\Controllers;

use App\Models\PartnershipCourse;
use App\Services\PartnershipCourseService;
use Illuminate\Http\Request;

class PartnershipCourseController extends Controller
{
    protected PartnershipCourseService $partnershipCourseService;

    public function __construct(PartnershipCourseService $partnershipCourseService)
    {
        $this->partnershipCourseService = $partnershipCourseService;
    }

    /**
     * Afficher la page de cours de partenariat
     */
    public function index()
    {
        return $this->partnershipCourseService->index();
    }

    /**
     * Traiter la soumission du formulaire de cours de partenariat
     */
    public function store(Request $request, $locale)
    {
        return $this->partnershipCourseService->store($request, $locale);
    }

    /**
     * Afficher la liste des cours de partenariat (pour l'admin)
     */
    public function admin()
    {
        return $this->partnershipCourseService->admin();
    }

    /**
     * Mettre à jour le statut d'un cours de partenariat
     */
    public function updateStatus(Request $request, PartnershipCourse $partnershipCourse)
    {
        return $this->partnershipCourseService->updateStatus($request, $partnershipCourse);
    }
}
