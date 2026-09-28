<?php

namespace App\Http\Controllers;

use App\Models\ProductStudy;
use App\Services\StudyService;

class StudyController extends Controller
{
    protected StudyService $studyService;

    public function __construct(StudyService $studyService)
    {
        $this->studyService = $studyService;
    }

    public function show($locale, ProductStudy $study)
    {
        return $this->studyService->show($locale, $study);
    }

    public function showPdf($locale, ProductStudy $study)
    {
        return $this->studyService->showPdf($locale, $study);
    }

    public function showVideos($locale, ProductStudy $study)
    {
        return $this->studyService->showVideos($locale, $study);
    }

    public function showVideosTemplate($locale, ProductStudy $study)
    {
        return $this->studyService->showVideosTemplate($locale, $study);
    }
}
