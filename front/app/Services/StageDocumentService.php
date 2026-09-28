<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StageDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StageDocumentService
{
    public function getCourseDocuments($productId)
    {
        $product = Product::findOrFail($productId);

        if (!Auth::check() || !$product->hasStudentAccess(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this course',
            ], 403);
        }

        $documents = StageDocument::where('product_id', $productId)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'type_name' => $doc->type_name,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->formatted_size,
                    'download_url' => route('stage-documents.download', $doc->id),
                    'created_at' => $doc->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'documents' => $documents,
        ]);
    }

    public function download($locale, $document)
    {
        $doc = StageDocument::findOrFail($document);

        $product = $doc->product;
        if (!Auth::check() || !$product->hasStudentAccess(Auth::id())) {
            abort(403, 'Unauthorized');
        }

        $adminStoragePath = base_path('../admin/storage/app/public/' . $doc->file_path);

        if (!file_exists($adminStoragePath)) {
            Log::error('Stage document file not found', [
                'document_id' => $doc->id,
                'file_path' => $doc->file_path,
                'attempted_path' => $adminStoragePath,
                'file_exists' => file_exists($adminStoragePath),
            ]);
            abort(404, 'File not found');
        }

        return response()->download($adminStoragePath, $doc->file_name);
    }
}
