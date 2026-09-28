<?php

namespace App\Http\Controllers;

use App\Services\StudentVideoExamService;
use Illuminate\Http\Request;

class StudentVideoExamController extends Controller
{
    protected StudentVideoExamService $studentVideoExamService;

    public function __construct(StudentVideoExamService $studentVideoExamService)
    {
        $this->studentVideoExamService = $studentVideoExamService;
    }

    /**
     * Soumettre un examen vidéo
     */
    public function store(Request $request)
    {
        return $this->studentVideoExamService->store($request);
    }

    /**
     * Obtenir le statut de soumission d'un étudiant pour un produit
     */
    public function getStudentSubmission($productId)
    {
        return $this->studentVideoExamService->getStudentSubmission($productId);
    }

    /**
     * Obtenir toutes les soumissions d'examens vidéo (pour les admins)
     */
    public function index(Request $request)
    {
        return $this->studentVideoExamService->index($request);
    }

    /**
     * Mettre à jour le statut d'une soumission (pour les admins)
     */
    public function updateStatus(Request $request, $id)
    {
        return $this->studentVideoExamService->updateStatus($request, $id);
    }
}
