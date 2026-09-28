<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImportStudentService;

class ImportStudentController extends Controller
{
    protected ImportStudentService $importStudentService;

    public function __construct(ImportStudentService $importStudentService)
    {
        $this->importStudentService = $importStudentService;
    }

    /**
     * Afficher la page d'importation des étudiants
     */
    public function index()
    {
        return $this->importStudentService->index();
    }

    /**
     * Traiter l'importation des étudiants depuis Excel
     */
    public function import(Request $request)
    {
        return $this->importStudentService->import($request);
    }

    /**
     * Ajouter un étudiant manuellement
     */
    public function addManual(Request $request)
    {
        return $this->importStudentService->addManual($request);
    }
}
