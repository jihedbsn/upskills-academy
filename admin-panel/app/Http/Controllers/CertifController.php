<?php

namespace App\Http\Controllers;

use App\Models\Certif;
use App\Services\CertifService;
use Illuminate\Http\Request;

class CertifController extends Controller
{
    protected CertifService $certifService;

    public function __construct(CertifService $certifService)
    {
        $this->certifService = $certifService;
    }

    public function index()
    {
        return $this->certifService->index();
    }

    public function create()
    {
        return $this->certifService->create();
    }

    public function store(Request $request)
    {
        return $this->certifService->store($request);
    }

    public function show(Certif $certif)
    {
        return $this->certifService->show($certif);
    }

    public function edit(Certif $certif)
    {
        return $this->certifService->edit($certif);
    }

    public function update(Request $request, Certif $certif)
    {
        return $this->certifService->update($request, $certif);
    }

    public function destroy(Certif $certif)
    {
        return $this->certifService->destroy($certif);
    }

    public function download(Certif $certif)
    {
        return $this->certifService->download($certif);
    }

    public function updateTemplate(Request $request, Certif $certif)
    {
        return $this->certifService->updateTemplate($request, $certif);
    }

    public function getTemplateData(Certif $certif)
    {
        return $this->certifService->getTemplateData($certif);
    }

    public function updateTemplateData(Request $request, Certif $certif)
    {
        return $this->certifService->updateTemplateData($request, $certif);
    }

    public function editClick(Certif $certif)
    {
        return $this->certifService->editClick($certif);
    }

    public function debugDatabase(Certif $certif)
    {
        return $this->certifService->debugDatabase($certif);
    }

    public function generateCertificate(Request $request, Certif $certif)
    {
        return $this->certifService->generateCertificate($request, $certif);
    }

    public function testGenerateCertificate(Request $request, Certif $certif)
    {
        return $this->certifService->testGenerateCertificate($request, $certif);
    }

    /**
     * Add a new dynamic field to the certificate
     */
    public function addDynamicField(Request $request, Certif $certif)
    {
        return $this->certifService->addDynamicField($request, $certif);
    }

    /**
     * Remove a dynamic field from the certificate
     */
    public function removeDynamicField(Request $request, Certif $certif)
    {
        return $this->certifService->removeDynamicField($request, $certif);
    }

    /**
     * Get dynamic fields for a certificate
     */
    public function getDynamicFields(Certif $certif)
    {
        return $this->certifService->getDynamicFields($certif);
    }

    /**
     * Download test certificate from editor
     */
    public function downloadTestCertificate(Certif $certif, $filename)
    {
        return $this->certifService->downloadTestCertificate($certif, $filename);
    }

}
