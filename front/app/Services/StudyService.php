<?php

namespace App\Services;

use App\Models\ProductStudy;
use Illuminate\Support\Facades\Auth;

class StudyService
{
    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    private function checkContentAccess(ProductStudy $study): bool
    {
        if (!Auth::check() || !$study->product) {
            return true;
        }

        $productId = $study->products_id;
        $studyId = $study->id;
        $studentId = Auth::id();

        return $this->installmentService->isContentAccessible($productId, $studyId, $studentId);
    }

    private function denyIfCourseBlocked(string $locale, ProductStudy $study)
    {
        if (!$study->product) {
            return null;
        }

        return app(BlockStudentCourseService::class)->denyIfBlocked($study->products_id, $locale);
    }

    public function show($locale, ProductStudy $study)
    {
        $study->load(['product', 'resource']);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        if ($blockResponse = $this->denyIfCourseBlocked($locale, $study)) {
            return $blockResponse;
        }

        if (!$study->product->hasStudentAccess(Auth::id())) {
            abort(403, __('messages.course_access_denied'));
        }

        if (!$this->checkContentAccess($study)) {
            return redirect()->back()->with('error', __('messages.content_locked'));
        }

        return view('studies.show', compact('study'));
    }

    public function showPdf($locale, ProductStudy $study)
    {
        $study->load(['product', 'resource']);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        if ($blockResponse = $this->denyIfCourseBlocked($locale, $study)) {
            return $blockResponse;
        }

        if (!$study->product->hasStudentAccess(Auth::id())) {
            abort(403, __('messages.course_access_denied'));
        }

        if (!$this->checkContentAccess($study)) {
            return redirect()->back()->with('error', __('messages.content_locked'));
        }

        if (!$study->resource || !in_array($study->resource->type, ['pdf', 'document', 'book'])) {
            abort(404);
        }

        return view('studies.pdf', compact('study'));
    }

    public function showVideos($locale, ProductStudy $study)
    {
        $study->load(['product', 'resource']);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        if ($blockResponse = $this->denyIfCourseBlocked($locale, $study)) {
            return $blockResponse;
        }

        if (!$study->product->hasStudentAccess(Auth::id())) {
            abort(403, __('messages.course_access_denied'));
        }

        if (!$this->checkContentAccess($study)) {
            return redirect()->back()->with('error', __('messages.content_locked'));
        }

        if (!$study->resource || !in_array($study->resource->type, ['mp4', 'video'])) {
            abort(404);
        }

        return view('studies.playlist', compact('study'));
    }

    public function showVideosTemplate($locale, ProductStudy $study)
    {
        $study->load(['product', 'resource']);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        if ($blockResponse = $this->denyIfCourseBlocked($locale, $study)) {
            return $blockResponse;
        }

        if (!$study->product->hasStudentAccess(Auth::id())) {
            abort(403, __('messages.course_access_denied'));
        }

        if (!$this->checkContentAccess($study)) {
            return redirect()->back()->with('error', __('messages.content_locked'));
        }

        if (!$study->resource ||
            !in_array($study->resource->type, ['mp4', 'video']) ||
            !$study->resource->hasMultipleVideos()) {
            abort(404);
        }

        return view('studies.videos-template', compact('study'));
    }
}
