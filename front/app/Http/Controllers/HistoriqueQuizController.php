<?php

namespace App\Http\Controllers;

use App\Services\HistoriqueQuizService;
use Illuminate\Http\Request;

class HistoriqueQuizController extends Controller
{
    protected HistoriqueQuizService $historiqueQuizService;

    public function __construct(HistoriqueQuizService $historiqueQuizService)
    {
        $this->historiqueQuizService = $historiqueQuizService;
    }

    /**
     * Afficher l'historique des quiz d'un étudiant pour un cours
     */
    public function index(Request $request, $productId = null)
    {
        return $this->historiqueQuizService->index($request, $productId);
    }

    /**
     * API: Obtenir l'historique des quiz d'un étudiant
     */
    public function getStudentHistory(Request $request, $studentId)
    {
        return $this->historiqueQuizService->getStudentHistory($request, $studentId);
    }

    /**
     * API: Obtenir les statistiques d'un étudiant
     */
    public function getStudentStats(Request $request, $studentId)
    {
        return $this->historiqueQuizService->getStudentStats($request, $studentId);
    }

    /**
     * API: Obtenir les statistiques d'un cours (Admin seulement)
     */
    public function getProductStats(Request $request, $productId)
    {
        return $this->historiqueQuizService->getProductStats($productId);
    }

    /**
     * Afficher les détails d'une tentative de quiz
     */
    public function show($id)
    {
        return $this->historiqueQuizService->show($id);
    }

    /**
     * API: Obtenir l'historique détaillé d'un quiz
     */
    public function getQuizHistory(Request $request, $studentId, $quizId)
    {
        return $this->historiqueQuizService->getQuizHistory($studentId, $quizId);
    }

    /**
     * API: Obtenir les statistiques globales (Admin seulement)
     */
    public function getGlobalStats()
    {
        return $this->historiqueQuizService->getGlobalStats();
    }
}
