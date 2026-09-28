<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    protected EmailTemplateService $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->emailTemplateService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->emailTemplateService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->emailTemplateService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->show($emailTemplate);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->edit($emailTemplate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->update($request, $emailTemplate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->destroy($emailTemplate);
    }

    /**
     * Preview template with sample data
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->preview($emailTemplate);
    }

    /**
     * Toggle template active status
     */
    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        return $this->emailTemplateService->toggleStatus($emailTemplate);
    }
}
