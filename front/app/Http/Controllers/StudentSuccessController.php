<?php

namespace App\Http\Controllers;

use App\Services\StudentSuccessService;
use Illuminate\Http\Request;

class StudentSuccessController extends Controller
{
    protected StudentSuccessService $studentSuccessService;

    public function __construct(StudentSuccessService $studentSuccessService)
    {
        $this->studentSuccessService = $studentSuccessService;
    }

    /**
     * Soumettre la réussite d'un étudiant pour un cours
     */
    public function submit(Request $request, $productId)
    {
        return $this->studentSuccessService->submit($request, $productId);
    }

    /**
     * Obtenir le statut de réussite d'un étudiant pour un cours
     */
    public function getStatus($productId)
    {
        return $this->studentSuccessService->getStatus($productId);
    }

    /**
     * Valider ou rejeter une soumission (Admin seulement)
     */
    public function validate(Request $request, $productId, $studentId)
    {
        return $this->studentSuccessService->validate($request, $productId, $studentId);
    }

    /**
     * Obtenir toutes les soumissions pour un cours (Admin seulement)
     */
    public function getSubmissions($productId)
    {
        return $this->studentSuccessService->getSubmissions($productId);
    }

    /**
     * Obtenir les statistiques de réussite pour un cours
     */
    public function getStatistics($productId)
    {
        return $this->studentSuccessService->getStatistics($productId);
    }
}
