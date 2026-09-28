<?php

namespace App\Http\Controllers;

use App\Models\StudentSuccess;
use App\Services\StudentSuccessService;
use Illuminate\Http\Request;

class StudentSuccessController extends Controller
{
    public function __construct(private StudentSuccessService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('student-successes.index', $data);
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('student-successes.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(StudentSuccess $studentSuccess)
    {
        $studentSuccess = $this->service->show($studentSuccess);
        return view('student-successes.show', compact('studentSuccess'));
    }

    public function edit(StudentSuccess $studentSuccess)
    {
        $data = $this->service->getEditData($studentSuccess);
        return view('student-successes.edit', $data);
    }

    public function update(Request $request, StudentSuccess $studentSuccess)
    {
        return $this->service->update($request, $studentSuccess);
    }

    public function destroy(StudentSuccess $studentSuccess)
    {
        return $this->service->destroy($studentSuccess);
    }

    public function openVideo(StudentSuccess $studentSuccess)
    {
        return redirect($studentSuccess->lien_video);
    }

    public function validate(StudentSuccess $studentSuccess)
    {
        return $this->service->validate($studentSuccess);
    }

    public function reject(StudentSuccess $studentSuccess)
    {
        return $this->service->reject($studentSuccess);
    }

    public function downloadCertificate(StudentSuccess $studentSuccess)
    {
        return $this->service->downloadCertificate($studentSuccess);
    }

    public function testGenerateCertificate(StudentSuccess $studentSuccess)
    {
        return $this->service->testGenerateCertificate($studentSuccess);
    }

    public function testGenerateCertificateAjax(StudentSuccess $studentSuccess)
    {
        return $this->service->testGenerateCertificateAjax($studentSuccess);
    }

    public function downloadTestCertificate(StudentSuccess $studentSuccess)
    {
        return $this->service->downloadTestCertificate($studentSuccess);
    }

    public function byProduct(Request $request)
    {
        return $this->service->byProduct($request);
    }

    public function generateCertificateDirect(StudentSuccess $studentSuccess)
    {
        return $this->service->generateCertificateDirect($studentSuccess);
    }
}
