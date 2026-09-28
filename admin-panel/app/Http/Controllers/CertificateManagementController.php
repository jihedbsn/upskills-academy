<?php

namespace App\Http\Controllers;

use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Services\CertificateManagementService;
use Illuminate\Http\Request;

class CertificateManagementController extends Controller
{
    protected CertificateManagementService $certificateManagementService;

    public function __construct(CertificateManagementService $certificateManagementService)
    {
        $this->certificateManagementService = $certificateManagementService;
    }

    public function index(Request $request)
    {
        return $this->certificateManagementService->index($request);
    }

    public function show(StudentSuccess $studentSuccess)
    {
        return $this->certificateManagementService->show($studentSuccess);
    }

    public function download(StudentSuccess $studentSuccess)
    {
        return $this->certificateManagementService->download($studentSuccess);
    }

    public function generate(Request $request, StudentSuccess $studentSuccess)
    {
        return $this->certificateManagementService->generate($request, $studentSuccess);
    }

    public function bulkGenerate(Request $request)
    {
        return $this->certificateManagementService->bulkGenerate($request);
    }

    /**
     * Update the certificate date
     */
    public function updateDate(Request $request, CertifStudent $certificate)
    {
        return $this->certificateManagementService->updateDate($request, $certificate);
    }

    /**
     * Regenerate certificate with new date
     */
    public function regenerate(Request $request, CertifStudent $certificate)
    {
        return $this->certificateManagementService->regenerate($request, $certificate);
    }

    /**
     * Delete a certificate request (removes entire StudentSuccess record)
     */
    public function delete($studentSuccessId)
    {
        return $this->certificateManagementService->delete($studentSuccessId);
    }

    /**
     * Delete only the generated certificate file/record (keeps StudentSuccess).
     */
    public function deleteGeneratedCertificate(CertifStudent $certificate)
    {
        return $this->certificateManagementService->deleteGeneratedCertificate($certificate);
    }

    /**
     * Afficher le certificat publiquement via le numéro de série (pour QR code)
     */
    public function viewPublic($serialNumber)
    {
        return $this->certificateManagementService->viewPublic($serialNumber);
    }

    /**
     * Show form to manually generate a certificate
     */
    public function create()
    {
        return $this->certificateManagementService->create();
    }

    /**
     * Get students by course (for AJAX call)
     */
    public function getStudentsByCourse($courseId)
    {
        return $this->certificateManagementService->getStudentsByCourse($courseId);
    }

    /**
     * Manually generate a certificate for a student and course
     */
    public function manualGenerate(Request $request)
    {
        return $this->certificateManagementService->manualGenerate($request);
    }
}
