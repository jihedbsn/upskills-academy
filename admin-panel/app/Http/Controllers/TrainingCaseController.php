<?php

namespace App\Http\Controllers;

use App\Models\TrainingCaseFile;
use App\Services\TrainingCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingCaseController extends Controller
{
    public function __construct(private TrainingCaseService $service) {}

    public function list()
    {
        return $this->service->list();
    }

    public function index()
    {
        $trainingCases = $this->service->index();
        return view('training-cases.index', compact('trainingCases'));
    }

    public function create()
    {
        return view('training-cases.create');
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function edit($id)
    {
        $trainingCase = $this->service->getEditData($id);
        return view('training-cases.edit', compact('trainingCase'));
    }

    public function update(Request $request, $id)
    {
        return $this->service->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }

    public function deleteFile($id)
    {
        return $this->service->deleteFile($id);
    }

    public function downloadFile($id)
    {
        $file = TrainingCaseFile::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function toggleStatus($id)
    {
        return $this->service->toggleStatus($id);
    }
}
