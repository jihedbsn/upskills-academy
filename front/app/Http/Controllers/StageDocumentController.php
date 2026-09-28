<?php

namespace App\Http\Controllers;

use App\Services\StageDocumentService;

class StageDocumentController extends Controller
{
    protected StageDocumentService $stageDocumentService;

    public function __construct(StageDocumentService $stageDocumentService)
    {
        $this->stageDocumentService = $stageDocumentService;
    }

    /**
     * Get documents for a course (student view)
     */
    public function getCourseDocuments($productId)
    {
        return $this->stageDocumentService->getCourseDocuments($productId);
    }

    /**
     * Download a stage document
     */
    public function download($locale, $document)
    {
        return $this->stageDocumentService->download($locale, $document);
    }
}
