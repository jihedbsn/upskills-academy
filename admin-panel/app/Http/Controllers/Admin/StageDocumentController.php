<?php

namespace App\Http\Controllers\Admin;

use App\Models\StageDocument;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StageDocumentController extends Controller
{
    /**
     * Upload or update a stage document for a product
     */
    public function upload(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'document_type' => 'required|in:request_letter,evaluation_form',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        $product = Product::findOrFail($request->product_id);

        try {
            // Delete old document if exists
            $existing = StageDocument::where('product_id', $request->product_id)
                ->where('document_type', $request->document_type)
                ->first();

            if ($existing) {
                if (Storage::disk('public')->exists($existing->file_path)) {
                    Storage::disk('public')->delete($existing->file_path);
                }
                $existing->delete();
            }

            // Store new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('stage-documents', $fileName, 'public');

            // Create document record
            $document = StageDocument::create([
                'product_id' => $request->product_id,
                'document_type' => $request->document_type,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            \Log::error('Stage document upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get documents for a product
     */
    public function getProductDocuments($productId)
    {
        $documents = StageDocument::where('product_id', $productId)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'product_id' => $doc->product_id,
                    'document_type' => $doc->document_type,
                    'type_name' => $doc->type_name,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->formatted_size,
                    'mime_type' => $doc->mime_type,
                    'download_url' => $doc->download_url,
                    'created_at' => $doc->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    /**
     * Delete a stage document
     */
    public function delete($documentId)
    {
        try {
            $document = StageDocument::findOrFail($documentId);

            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Stage document delete error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document: ' . $e->getMessage()
            ], 500);
        }
    }
}
