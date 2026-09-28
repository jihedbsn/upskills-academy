<?php

namespace App\Services;

use App\Models\Certif;
use App\Models\Product;
use App\Models\Student;
use App\Models\StudentSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CertifService
{
    public function index()
    {
        $certifs = Certif::orderBy('created_at', 'desc')->paginate(10);

        return view('certifs.index', compact('certifs'));
    }

    public function create()
    {
        return view('certifs.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $uploadPath = public_path('uploads/certif');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName . '.' . $extension);

                $certif = Certif::create([
                    'nom' => $request->nom,
                    'file_url' => 'uploads/certif/' . $fileName . '.' . $extension,
                    'image_url' => 'uploads/certif/' . $fileName . '.' . $extension,
                    'template_data' => Certif::getDefaultTemplateData(),
                    'orientation' => 'vertical',
                    'is_active' => true,
                ]);

                return redirect()->route('certifs.edit', $certif)
                    ->with('success', 'Certificat créé avec succès ! Vous pouvez maintenant configurer les positions des éléments.');
            }

            return redirect()->back()
                ->withErrors(['error' => 'Aucun fichier n\'a été fourni'])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Certif $certif)
    {
        return view('certifs.show', compact('certif'));
    }

    public function edit(Certif $certif)
    {
        $imageDimensions = $this->getImageDimensions($certif);

        return view('certifs.edit', compact('certif', 'imageDimensions'));
    }

    public function update(Request $request, Certif $certif)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:10240',
            'template_data' => 'nullable|array',
            'orientation' => 'nullable|in:vertical,horizontal',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'nom' => $request->nom,
                'orientation' => $request->orientation ?? 'vertical',
                'is_active' => $request->has('is_active'),
            ];

            if ($request->hasFile('file')) {
                if ($certif->file_url && file_exists(public_path($certif->getRawOriginal('file_url')))) {
                    unlink(public_path($certif->getRawOriginal('file_url')));
                }
                if ($certif->image_url && file_exists(public_path($certif->getRawOriginal('image_url')))) {
                    unlink(public_path($certif->getRawOriginal('image_url')));
                }

                $file = $request->file('file');
                $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $uploadPath = public_path('uploads/certif');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName . '.' . $extension);

                $data['file_url'] = 'uploads/certif/' . $fileName . '.' . $extension;
                $data['image_url'] = 'uploads/certif/' . $fileName . '.' . $extension;
            }

            if ($request->has('template_data')) {
                $data['template_data'] = $request->template_data;
            }

            $certif->update($data);

            return redirect()->route('certifs.index')
                ->with('success', 'Certificat mis à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Certif $certif)
    {
        try {
            if ($certif->file_url && file_exists(public_path($certif->getRawOriginal('file_url')))) {
                unlink(public_path($certif->getRawOriginal('file_url')));
            }
            if ($certif->image_url && file_exists(public_path($certif->getRawOriginal('image_url')))) {
                unlink(public_path($certif->getRawOriginal('image_url')));
            }

            $certif->delete();

            return redirect()->route('certifs.index')
                ->with('success', 'Certificat supprimé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function download(Certif $certif)
    {
        try {
            $fileUrl = $certif->getRawOriginal('file_url');

            if (filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($fileUrl);
                $relativePath = ltrim($parsedUrl['path'] ?? '', '/');
                $filePath = public_path($relativePath);
            } else {
                $filePath = public_path($fileUrl);
            }

            if (file_exists($filePath)) {
                return response()->download($filePath);
            }

            Log::error('Certificate file not found', [
                'certif_id' => $certif->id,
                'file_url' => $fileUrl,
                'attempted_path' => $filePath
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Fichier non trouvé. Le certificat n\'existe peut-être pas encore.']);
        } catch (\Exception $e) {
            Log::error('Certificate download error', [
                'certif_id' => $certif->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors du téléchargement: ' . $e->getMessage()]);
        }
    }

    public function updateTemplate(Request $request, Certif $certif)
    {
        $validator = Validator::make($request->all(), [
            'template_data' => 'required|array',
            'orientation' => 'nullable|in:vertical,horizontal',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $certif->update([
                'template_data' => $request->template_data,
                'orientation' => $request->orientation ?? 'vertical',
            ]);

            return response()->json(['success' => true, 'message' => 'Template mis à jour avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function getTemplateData(Certif $certif)
    {
        try {
            $templateData = $certif->template_data ?: [];

            return response()->json([
                'success' => true,
                'template_data' => $templateData,
                'orientation' => $certif->orientation ?? 'vertical'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function updateTemplateData(Request $request, Certif $certif)
    {
        try {
            Log::info('🔄 Début de la sauvegarde template_data', [
                'certif_id' => $certif->id,
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'template_data' => 'required|array',
            ]);

            if ($validator->fails()) {
                Log::error('❌ Validation échouée', [
                    'errors' => $validator->errors()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('📊 Données template_data reçues', [
                'template_data' => $request->template_data
            ]);

            Log::info('📊 Données avant sauvegarde', [
                'certif_id' => $certif->id,
                'current_template_data' => $certif->template_data,
                'new_template_data' => $request->template_data
            ]);

            $templateData = $request->template_data;

            if ($certif->image_url) {
                $imagePath = $this->getImagePath($certif);
                if ($imagePath && file_exists($imagePath)) {
                    $imageSize = getimagesize($imagePath);
                    if ($imageSize) {
                        $templateData['_reference_width'] = $imageSize[0];
                        $templateData['_reference_height'] = $imageSize[1];
                        Log::info('📐 Dimensions de référence capturées/mises à jour', [
                            'width' => $imageSize[0],
                            'height' => $imageSize[1],
                            'image_path' => $imagePath,
                            'forced_update' => isset($request->template_data['_reference_width'])
                        ]);
                    }
                }
            }

            $updated = $certif->update([
                'template_data' => $templateData
            ]);

            Log::info('💾 Résultat de la mise à jour', [
                'updated' => $updated,
                'certif_id' => $certif->id
            ]);

            $certif->refresh();

            Log::info('✅ Template sauvegardé avec succès', [
                'certif_id' => $certif->id,
                'saved_template_data' => $certif->template_data,
                'template_data_type' => gettype($certif->template_data)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template sauvegardé avec succès',
                'template_data' => $certif->fresh()->template_data
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la sauvegarde', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editClick(Certif $certif)
    {
        return view('certifs.edit-click', compact('certif'));
    }

    public function debugDatabase(Certif $certif)
    {
        Log::info('🔍 Debug base de données', [
            'certif_id' => $certif->id,
            'template_data_raw' => $certif->getRawOriginal('template_data'),
            'template_data_processed' => $certif->template_data,
            'template_data_type' => gettype($certif->template_data),
            'all_attributes' => $certif->getAttributes()
        ]);

        return response()->json([
            'success' => true,
            'certif_id' => $certif->id,
            'template_data_raw' => $certif->getRawOriginal('template_data'),
            'template_data_processed' => $certif->template_data,
            'template_data_type' => gettype($certif->template_data),
            'all_attributes' => $certif->getAttributes()
        ]);
    }

    public function generateCertificate(Request $request, Certif $certif)
    {
        $validator = Validator::make($request->all(), [
            'fullname_en' => 'required|string|max:255',
            'fullname_ar' => 'required|string|max:255',
            'date' => 'required|date',
            'serial_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Certificat généré avec succès',
                'data' => [
                    'fullname_en' => $request->fullname_en,
                    'fullname_ar' => $request->fullname_ar,
                    'date' => $request->date,
                    'serial_number' => $request->serial_number,
                    'qr_code' => 'QR_' . $request->serial_number,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function testGenerateCertificate(Request $request, Certif $certif)
    {
        try {
            $certificateService = new CertificateGeneratorService();

            $testStudentSuccess = new StudentSuccess([
                'student_id' => 1,
                'product_id' => 1,
                'success' => 1,
                'validated_at' => now(),
            ]);

            $fullnameEn = $request->fullname_en ?? 'John Doe';
            $nameParts = explode(' ', $fullnameEn, 2);
            $firstName = $nameParts[0] ?? 'John';
            $lastName = $nameParts[1] ?? 'Doe';

            $testStudent = new Student([
                'id' => 1,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => 'test@example.com',
            ]);

            $testProduct = new Product([
                'id' => 1,
                'titre' => 'Test Course',
                'certif_id' => $certif->id,
            ]);

            $testStudentSuccess->setRelation('student', $testStudent);
            $testStudentSuccess->setRelation('product', $testProduct);
            $testProduct->setRelation('certif', $certif);

            if ($request->has('template_data')) {
                $templateData = $request->template_data;

                if (!isset($templateData['_reference_width']) || !isset($templateData['_reference_height'])) {
                    $imagePath = $this->getImagePath($certif);
                    if ($imagePath && file_exists($imagePath)) {
                        $imageSize = getimagesize($imagePath);
                        if ($imageSize) {
                            $templateData['_reference_width'] = $imageSize[0];
                            $templateData['_reference_height'] = $imageSize[1];
                            Log::info('📐 Reference dimensions added for test certificate:', [
                                'width' => $imageSize[0],
                                'height' => $imageSize[1]
                            ]);
                        }
                    }
                }

                $certif->template_data = $templateData;
                Log::info('🎯 ÉDITEUR - Template data reçu pour test depuis l\'éditeur:', [
                    'certif_id' => $certif->id,
                    'template_data' => $templateData,
                    'template_data_json' => json_encode($templateData),
                    'fullname_en' => $request->fullname_en ?? 'John Doe',
                    'image_path' => $certif->getRawOriginal('image_url'),
                    'reference_width' => $templateData['_reference_width'] ?? 'NOT_SET',
                    'reference_height' => $templateData['_reference_height'] ?? 'NOT_SET',
                    'name_student_position' => $templateData['name_student'] ?? 'NOT_FOUND',
                    'date_position' => $templateData['date'] ?? 'NOT_FOUND',
                    'serial_position' => $templateData['serial_number'] ?? 'NOT_FOUND',
                    'qr_position' => $templateData['qr_code'] ?? 'NOT_FOUND'
                ]);
            }

            $testResult = $certificateService->generateTestCertificate(
                $certif,
                $request->fullname_en ?? 'John Doe',
                $request->date ?? now()->format('d/m/Y'),
                $request->serial_number ?? 'TEST-' . time()
            );

            return response()->json([
                'success' => true,
                'message' => 'Certificat de test généré avec succès',
                'file_path' => $testResult['file_path'],
                'serial_number' => $testResult['serial_number'],
                'download_url' => route('certifs.download-test-certificate', [$certif, basename($testResult['file_path'])]),
                'data' => [
                    'fullname_en' => $request->fullname_en ?? 'John Doe',
                    'fullname_ar' => $request->fullname_ar ?? 'جون دو',
                    'date' => $request->date ?? now()->format('Y-m-d'),
                    'serial_number' => $request->serial_number ?? 'TEST-' . time(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de test depuis l\'éditeur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ]);
        }
    }

    public function addDynamicField(Request $request, Certif $certif)
    {
        $validator = Validator::make($request->all(), [
            'field_key' => 'required|string|max:50|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,date,number,email,url',
            'default_text' => 'nullable|string|max:255',
            'x' => 'nullable|integer|min:0',
            'y' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:10|max:1000',
            'height' => 'nullable|integer|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $templateData = $certif->template_data ?: [];
            if (isset($templateData[$request->field_key])) {
                return response()->json(['success' => false, 'message' => 'Ce champ existe déjà']);
            }

            $fieldData = [
                'x' => $request->x ?? 100,
                'y' => $request->y ?? 100,
                'width' => $request->width ?? 200,
                'height' => $request->height ?? 30,
                'show' => true,
                'text' => $request->default_text ?? $request->field_name,
                'font_size' => 16,
                'color' => '#000000',
                'font_family' => 'Arial',
                'type' => $request->field_type,
                'is_dynamic' => true,
                'field_name' => $request->field_name
            ];

            $certif->addDynamicField($request->field_key, $fieldData);

            return response()->json([
                'success' => true,
                'message' => 'Champ dynamique ajouté avec succès',
                'field' => $fieldData
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function removeDynamicField(Request $request, Certif $certif)
    {
        $validator = Validator::make($request->all(), [
            'field_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $removed = $certif->removeDynamicField($request->field_key);

            if ($removed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Champ dynamique supprimé avec succès'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Champ non trouvé ou ne peut pas être supprimé'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function getDynamicFields(Certif $certif)
    {
        try {
            $dynamicFields = $certif->getDynamicFields();

            return response()->json([
                'success' => true,
                'data' => $dynamicFields
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function downloadTestCertificate(Certif $certif, $filename)
    {
        try {
            $filePath = public_path('upload/certif-student/' . $filename);

            if (!file_exists($filePath)) {
                return response()->json(['success' => false, 'message' => 'Fichier certificat de test non trouvé']);
            }

            return response()->download($filePath, 'certificat_test_' . $certif->nom . '_' . time() . '.png');
        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement du certificat de test depuis l\'éditeur: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors du téléchargement: ' . $e->getMessage()]);
        }
    }

    private function getImageDimensions(Certif $certif)
    {
        $imagePath = $certif->getRawOriginal('image_url');

        if (!$imagePath || !file_exists(public_path($imagePath))) {
            return [
                'width' => 800,
                'height' => 600,
                'exists' => false
            ];
        }

        $fullPath = public_path($imagePath);
        $imageInfo = getimagesize($fullPath);

        if (!$imageInfo) {
            return [
                'width' => 800,
                'height' => 600,
                'exists' => false
            ];
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'exists' => true,
            'path' => $imagePath,
            'url' => asset($imagePath)
        ];
    }

    private function getImagePath(Certif $certif)
    {
        if (!$certif->image_url) {
            return null;
        }

        if (str_starts_with($certif->image_url, 'http')) {
            return $certif->image_url;
        }

        $localPath = public_path($certif->image_url);
        if (file_exists($localPath)) {
            return $localPath;
        }

        return null;
    }
}
