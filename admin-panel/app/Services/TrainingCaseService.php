<?php

namespace App\Services;

use App\Models\TrainingCase;
use App\Models\TrainingCaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TrainingCaseService
{
    public function list()
    {
        return TrainingCase::where('is_active', true)
            ->withCount('files')
            ->orderBy('name')
            ->get(['id', 'name', 'description']);
    }

    public function index(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return TrainingCase::withCount(['products', 'files'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'files'       => 'required|array|min:1',
            'files.*'     => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $trainingCase = TrainingCase::create([
                'name'        => $request->name,
                'description' => $request->description,
                'is_active'   => $request->has('is_active'),
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('training_cases', $fileName, 'public');

                    TrainingCaseFile::create([
                        'training_case_id' => $trainingCase->id,
                        'file_name'        => $file->getClientOriginalName(),
                        'file_path'        => $filePath,
                        'file_type'        => $file->getClientOriginalExtension(),
                        'file_size'        => $file->getSize(),
                        'order'            => $index,
                    ]);
                }
            }

            return redirect()->route('training-cases.index')->with('success', 'Training case created successfully!');
        } catch (\Exception $e) {
            Log::error('Training case creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create training case: ' . $e->getMessage());
        }
    }

    public function getEditData($id): TrainingCase
    {
        return TrainingCase::with('files')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $trainingCase = TrainingCase::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'files.*'     => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $trainingCase->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->has('is_active'),
        ]);

        if ($request->hasFile('files')) {
            $currentMaxOrder = $trainingCase->files()->max('order') ?? -1;

            foreach ($request->file('files') as $index => $file) {
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('training_cases', $fileName, 'public');

                TrainingCaseFile::create([
                    'training_case_id' => $trainingCase->id,
                    'file_name'        => $file->getClientOriginalName(),
                    'file_path'        => $filePath,
                    'file_type'        => $file->getClientMimeType(),
                    'file_size'        => $file->getSize(),
                    'order'            => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('training-cases.index')->with('success', 'Training case updated successfully!');
    }

    public function destroy($id)
    {
        $trainingCase = TrainingCase::findOrFail($id);

        foreach ($trainingCase->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $trainingCase->delete();

        return redirect()->route('training-cases.index')->with('success', 'Training case deleted successfully!');
    }

    public function deleteFile($id)
    {
        $file = TrainingCaseFile::findOrFail($id);

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->back()->with('success', 'File deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $trainingCase = TrainingCase::findOrFail($id);
        $trainingCase->update(['is_active' => !$trainingCase->is_active]);

        return redirect()->back()->with('success', 'Training case status updated!');
    }
}
