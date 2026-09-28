<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\Country;
use App\Models\ProductVariation;
use App\Models\ProductStudy;
use App\Models\Resource;
use App\Models\Quiz;
use App\Models\TypeQuiz;
use App\Models\Certif;
use App\Models\ContentMilestone;
use App\Models\TrainingCase;
use App\Models\ProductStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductService
{
    private function extractMonthsFromPeriod(?string $period): int
    {
        if (empty($period)) {
            return 0;
        }

        $normalized = mb_strtolower(trim($period));
        $hasMonthUnit = preg_match('/month|months|mois|شهر|أشهر/u', $normalized) === 1;

        if (!$hasMonthUnit) {
            return 0;
        }

        if (!preg_match('/(\d+)/', $normalized, $matches)) {
            return 0;
        }

        return (int) $matches[1];
    }

    private function resolveValidityMonths(Request $request): ?int
    {
        $validityMonths = (int) ($request->validity_months ?? 0);

        if ($validityMonths < 1) {
            $validityMonths = $this->extractMonthsFromPeriod($request->period ?? null);
        }

        return $validityMonths > 0 ? $validityMonths : null;
    }

    /**
     * Splitting month for quizzes/exams. Preserved regardless of installment toggle so course/quiz
     * splitting works the same way whether installments are enabled or not. Null means "no split".
     */
    private function normalizeQuizInstallmentMonth(?int $month, bool $installmentEnabled, int $maxInstallmentMonth): ?int
    {
        $maxInstallmentMonth = max(1, $maxInstallmentMonth);
        $m = (int) ($month ?? 0);
        if ($m < 1) {
            return null;
        }
        if ($m > $maxInstallmentMonth) {
            $m = $maxInstallmentMonth;
        }

        return $m;
    }

    /**
     * Nullable positive integer — empty / zero means "no calendar-day gate" (backward compatible).
     */
    private function normalizeOpensAfterPurchaseDays(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $n = (int) $raw;
        if ($n < 1) {
            return null;
        }

        return min($n, 3650);
    }

    private function syncExistingEnrollmentExpirations(int $productId, int $validityMonths): void
    {
        if ($validityMonths < 1) {
            return;
        }

        ProductStudent::where('product_id', $productId)
            ->whereNotNull('access_granted_at')
            ->chunkById(200, function ($enrollments) use ($validityMonths) {
                foreach ($enrollments as $enrollment) {
                    $expectedExpiration = Carbon::parse($enrollment->access_granted_at)->copy()->addMonths($validityMonths);

                    if (!$enrollment->expiration_date || $enrollment->expiration_date->lt($expectedExpiration)) {
                        $enrollment->expiration_date = $expectedExpiration;

                        // Reactivate if enrollment had been marked expired before the corrected date.
                        if ($expectedExpiration->isFuture()) {
                            $enrollment->is_expired = false;
                            $enrollment->is_active = true;
                        }

                        $enrollment->save();
                    }
                }
            });
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'teacher', 'country']);

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('category', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('titre', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('teacher', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('nom', 'like', '%' . $searchTerm . '%')
                             ->orWhere('prenom', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('country', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('titre', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('langue', 'like', '%' . $searchTerm . '%')
                ->orWhere('statut', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getCreateData(): array
    {
        return [
            'categories'    => Category::all(),
            'teachers'      => Teacher::all(),
            'countries'     => Country::all(),
            'resources'     => Resource::all(),
            'certifs'       => Certif::where('is_active', true)->orderBy('nom')->get(),
            'quizzes'       => Quiz::with('type')->get(),
            'quizTypes'     => TypeQuiz::all(),
            'trainingCases' => TrainingCase::where('is_active', true)->get(),
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories_id' => 'required|exists:categories,id',
            'period' => 'required|string|max:255',
            'point' => 'required|integer|min:0',
            'iscach' => 'boolean',
            'video' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'promo_points' => 'nullable|integer|min:0',
            'validity_months' => 'nullable|integer|min:1',
            'teacher_id' => 'required|exists:teachers,id',
            'country_id' => 'required|exists:countries,id',
            'certif_id' => 'nullable|exists:certifs,id',
            'certificate_generation_mode' => 'nullable|in:manual,automatic',
            'type_course' => 'required|in:fi,pt,fa',
            'goverrnement' => 'boolean',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'prix' => 'nullable|numeric|min:0',
            'max_exam_attempts' => 'nullable|integer|min:1|max:100',
            'renewal_price' => 'nullable|numeric|min:0',
            'arabic_name' => 'required|string|max:255',
            'arabic_slug' => 'required|string|max:255',
            'arabic_short_description' => 'required|string',
            'arabic_description_exams' => 'required|string',
            'arabic_description_quizzes' => 'required|string',
            'arabic_description_final_exam' => 'nullable|string',
            'arabic_description_video_exam' => 'nullable|string',
            'arabic_description_stage' => 'nullable|string',
            'arabic_description_study_case' => 'nullable|string',
            'english_name' => 'required|string|max:255',
            'english_slug' => 'required|string|max:255',
            'english_short_description' => 'required|string',
            'english_description_exams' => 'required|string',
            'english_description_quizzes' => 'required|string',
            'english_description_final_exam' => 'nullable|string',
            'english_description_video_exam' => 'nullable|string',
            'english_description_stage' => 'nullable|string',
            'english_description_study_case' => 'nullable|string',
            'quiz_ids' => 'nullable|array',
            'quiz_ids.*' => 'exists:quizzes,id',
            'quiz_nb_questions' => 'nullable|array',
            'quiz_nb_questions.*' => 'nullable|integer|min:1',
            'quiz_scores' => 'nullable|array',
            'quiz_scores.*' => 'nullable|integer|min:0|max:100',
            'quiz_installment_months' => 'nullable|array',
            'quiz_installment_months.*' => 'nullable|integer|min:1',
            'quiz_opens_after_purchase_days' => 'nullable|array',
            'quiz_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'nullable|string',
            'exam_types' => 'nullable|array',
            'exam_types.*' => 'nullable|in:theoretical,practical',
            'practical_types' => 'nullable|array',
            'practical_types.*' => 'nullable|in:online,classroom',
            'exam_nb_questions' => 'nullable|array',
            'exam_nb_questions.*' => 'nullable|integer|min:1',
            'exam_scores' => 'nullable|array',
            'exam_scores.*' => 'nullable|integer|min:0|max:100',
            'exam_installment_months' => 'nullable|array',
            'exam_installment_months.*' => 'nullable|integer|min:1',
            'exam_opens_after_purchase_days' => 'nullable|array',
            'exam_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'is_stage' => 'boolean',
            'is_exam_video' => 'boolean',
            'is_classroom' => 'boolean',
            'is_zoom' => 'boolean',
            'is_online' => 'boolean',
            'study_resources' => 'nullable|array',
            'study_resources.*.resource_id' => 'required_with:study_resources|exists:resources,id',
        ]);

        $forceSave = $request->has('force_save') && $request->force_save == '1';

        if ($validator->fails()) {
            if ($forceSave) {
                Log::info('Enregistrement forcé avec erreurs de validation:', $validator->errors()->toArray());
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreurs de validation',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        try {
            DB::beginTransaction();

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $originalName = $image->getClientOriginalName();
                $extension = $image->getClientOriginalExtension();
                $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

                Log::info('=== DÉBUT UPLOAD IMAGE ===');
                Log::info('Nom original: ' . $originalName);
                Log::info('Extension: ' . $extension);
                Log::info('Nom sans extension: ' . $nameWithoutExtension);

                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameWithoutExtension);
                $maxNameLength = 50;
                if (strlen($cleanName) > $maxNameLength) {
                    $cleanName = substr($cleanName, 0, $maxNameLength);
                }

                $imageName = time() . '_' . $cleanName . '.' . $extension;

                Log::info('Nom nettoyé: ' . $cleanName);
                Log::info('Nom final généré: ' . $imageName);

                $imageUploadPath = public_path('uploads/products/images');
                if (!file_exists($imageUploadPath)) {
                    mkdir($imageUploadPath, 0755, true);
                }

                $image->move($imageUploadPath, $imageName);

                $fullPath = $imageUploadPath . '/' . $imageName;
                if (file_exists($fullPath)) {
                    Log::info('Fichier créé avec succès: ' . $fullPath);
                } else {
                    Log::error('ERREUR: Fichier non créé: ' . $fullPath);
                }

                Log::info('=== FIN UPLOAD IMAGE ===');
            }

            Log::info('=== AVANT CRÉATION PRODUIT ===');
            Log::info('Valeur imageName à sauvegarder: ' . $imageName);
            Log::info('Type de imageName: ' . gettype($imageName));
            Log::info('Longueur de imageName: ' . strlen($imageName));
            Log::info('=== FIN AVANT CRÉATION ===');

            $resolvedValidityMonths = $this->resolveValidityMonths($request);

            $product = Product::create([
                'iscach' => $request->has('iscach') ? 1 : 0,
                'statut' => 1,
                'categories_id' => $request->categories_id,
                'period' => $request->period,
                'point' => $request->point,
                'video' => $request->video,
                'image' => $imageName,
                'promo_points' => $request->promo_points,
                'validity_months' => $resolvedValidityMonths,
                'teacher_id' => $request->teacher_id,
                'country_id' => $request->country_id,
                'certif_id' => $request->certif_id,
                'certificate_generation_mode' => $request->certificate_generation_mode ?? 'manual',
                'type_course' => $request->type_course,
                'goverrnement' => $request->has('goverrnement') ? 1 : 0,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'prix' => $request->prix,
                'max_exam_attempts' => $request->max_exam_attempts ?? 3,
                'renewal_price' => $request->renewal_price ?? 50.00,
                'is_stage' => $request->has('is_stage') ? 1 : 0,
                'is_exam_video' => $request->has('is_exam_video') ? 1 : 0,
                'is_classroom' => $request->has('is_classroom') ? 1 : 0,
                'is_zoom' => $request->has('is_zoom') ? 1 : 0,
                'is_online' => $request->has('is_online') ? 1 : 0,
                'breuillant' => false,
                'has_theoretical_exam' => $request->has('has_theoretical_exam') ? 1 : 0,
                'has_practical_exam' => (!empty($request->practical_exam_type) || $request->has('has_practical_exam')) ? 1 : 0,
                'practical_exam_type' => $request->practical_exam_type,
                'is_listed' => $request->has('is_listed') ? 1 : 0,
            ]);

            ProductVariation::create([
                'products_id' => $product->id,
                'name' => $request->arabic_name,
                'slug' => $request->arabic_slug,
                'short_description' => $request->arabic_short_description,
                'description_the_exams' => $request->arabic_description_exams,
                'description_the_quizzes' => $request->arabic_description_quizzes,
                'description_final_exam' => $request->arabic_description_final_exam,
                'description_video_exam' => $request->arabic_description_video_exam,
                'description_stage' => $request->arabic_description_stage,
                'description_study_case' => $request->arabic_description_study_case,
                'langue' => 'ar',
            ]);

            ProductVariation::create([
                'products_id' => $product->id,
                'name' => $request->english_name,
                'slug' => $request->english_slug,
                'short_description' => $request->english_short_description,
                'description_the_exams' => $request->english_description_exams,
                'description_the_quizzes' => $request->english_description_quizzes,
                'description_final_exam' => $request->english_description_final_exam,
                'description_video_exam' => $request->english_description_video_exam,
                'description_stage' => $request->english_description_stage,
                'description_study_case' => $request->english_description_study_case,
                'langue' => 'en',
            ]);

            if ($request->has('study_resources') && is_array($request->study_resources) && !empty($request->study_resources)) {
                foreach ($request->study_resources as $index => $studyResource) {
                    if (!empty($studyResource['resource_id'])) {
                        $resource = Resource::find($studyResource['resource_id']);
                        if ($resource) {
                            $order = isset($studyResource['order']) ? (int)$studyResource['order'] : $index;
                            ProductStudy::create([
                                'products_id' => $product->id,
                                'name_ar' => $resource->name_ar,
                                'name_en' => $resource->name_en,
                                'resource_id' => $studyResource['resource_id'],
                                'lang' => null,
                                'order' => $order,
                            ]);
                        }
                    }
                }
            }

            $quizPivotData = [];

            if ($request->has('quiz_ids') && is_array($request->quiz_ids)) {
                $quizNbQuestions = $request->input('quiz_nb_questions', []);
                $quizScores = $request->input('quiz_scores', []);
                $quizInstallmentMonths = $request->input('quiz_installment_months', []);
                $installmentEnabled = $request->has('installment_allowed');
                $maxInstallmentMonth = max(1, (int) ($request->validity_months ?? 1));

                $quizOpensAfterPurchase = $request->input('quiz_opens_after_purchase_days', []);
                foreach ($request->quiz_ids as $index => $quizId) {
                    if (empty($quizId)) {
                        continue;
                    }
                    $quizId = (int) $quizId;
                    $nbQuestions = isset($quizNbQuestions[$index]) ? (int) $quizNbQuestions[$index] : 10;
                    $scoreSuccess = isset($quizScores[$index]) ? (int) $quizScores[$index] : 50;
                    $installmentMonth = isset($quizInstallmentMonths[$index]) && $quizInstallmentMonths[$index] !== ''
                        ? (int) $quizInstallmentMonths[$index]
                        : null;
                    $installmentMonth = $this->normalizeQuizInstallmentMonth($installmentMonth, $installmentEnabled, $maxInstallmentMonth);
                    $opensAfter = isset($quizOpensAfterPurchase[$index])
                        ? $this->normalizeOpensAfterPurchaseDays($quizOpensAfterPurchase[$index])
                        : null;
                    $quizPivotData[$quizId] = [
                        'nb_question_affiche' => max($nbQuestions, 1),
                        'score_success' => max(min($scoreSuccess, 100), 0),
                        'use_own_questions' => false,
                        'installment_month' => $installmentMonth,
                        'opens_after_purchase_days' => $opensAfter,
                    ];
                }
            }

            if ($request->has('exam_ids') && is_array($request->exam_ids)) {
                $examNbQuestions = $request->input('exam_nb_questions', []);
                $examScores = $request->input('exam_scores', []);
                $examTypes = $request->input('exam_types', []);
                $examUseOwnQuestions = $request->input('exam_use_own_questions', []);
                $examInstallmentMonths = $request->input('exam_installment_months', []);
                $installmentEnabled = $request->has('installment_allowed');
                $maxInstallmentMonth = max(1, (int) ($request->validity_months ?? 1));
                $practicalTypes = $request->input('practical_types', []);
                $trainingCaseIds = [];

                $examOpensAfterPurchase = $request->input('exam_opens_after_purchase_days', []);

                foreach ($request->exam_ids as $index => $examId) {
                    if (empty($examId)) {
                        continue;
                    }

                    $examType = isset($examTypes[$index]) ? $examTypes[$index] : 'theoretical';
                    $nbQuestions = isset($examNbQuestions[$index]) ? (int) $examNbQuestions[$index] : 10;
                    $scoreSuccess = isset($examScores[$index]) ? (int) $examScores[$index] : 50;

                    if ($examType === 'theoretical') {
                        $examIdInt = (int) $examId;
                        $useOwnQuestions = isset($examUseOwnQuestions[$examIdInt]) && $examUseOwnQuestions[$examIdInt] == '1';
                        $installmentMonth = isset($examInstallmentMonths[$index]) && $examInstallmentMonths[$index] !== ''
                            ? (int) $examInstallmentMonths[$index]
                            : null;
                        $installmentMonth = $this->normalizeQuizInstallmentMonth($installmentMonth, $installmentEnabled, $maxInstallmentMonth);
                        $examOpensRaw = isset($examOpensAfterPurchase[$index])
                            ? $examOpensAfterPurchase[$index]
                            : null;
                        $examOpensAfter = $this->normalizeOpensAfterPurchaseDays($examOpensRaw);
                        $quizPivotData[$examIdInt] = [
                            'nb_question_affiche' => max($nbQuestions, 1),
                            'score_success' => max(min($scoreSuccess, 100), 0),
                            'use_own_questions' => $useOwnQuestions,
                            'installment_month' => $installmentMonth,
                            'opens_after_purchase_days' => $examOpensAfter,
                        ];
                    } elseif ($examType === 'practical') {
                        if (strpos($examId, 'tc_') === 0) {
                            $trainingCaseId = (int) substr($examId, 3);
                            $trainingCaseIds[] = $trainingCaseId;
                            $practicalType = isset($practicalTypes[$index]) ? $practicalTypes[$index] : 'online';
                            // TODO: Store practical_type in a separate table if needed
                        }
                    }
                }

                if (!empty($trainingCaseIds)) {
                    $product->trainingCases()->attach($trainingCaseIds);
                }
            }

            if (!empty($quizPivotData)) {
                $product->quizzes()->attach($quizPivotData);
            }

            if ($request->has('has_practical_exam') && $request->has('training_case_ids')) {
                $product->trainingCases()->attach($request->training_case_ids);
            }

            $finalHasPracticalExam = !empty($request->practical_exam_type)
                || $request->has('has_practical_exam')
                || $product->trainingCases()->exists();

            $product->update([
                'has_practical_exam' => $finalHasPracticalExam ? 1 : 0,
            ]);

            DB::commit();

            Log::info('=== VÉRIFICATION BASE DE DONNÉES ===');
            Log::info('Nom de l\'image généré: ' . $imageName);
            Log::info('Nom de l\'image en base: ' . $product->image);
            Log::info('Correspondance: ' . ($imageName === $product->image ? 'OUI' : 'NON'));
            Log::info('=== FIN VÉRIFICATION ===');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produit créé avec succès ! ID: ' . $product->id,
                    'redirect' => route('products.index'),
                    'breuillant' => false
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Produit créé avec succès ! ID: ' . $product->id);

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du produit: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création du produit: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Product $product): Product
    {
        $product->load(['category', 'teacher', 'country', 'variations', 'types', 'studies.resource']);
        return $product;
    }

    public function getEditData(Product $product): array
    {
        $product->load([
            'variations',
            'types',
            'studies.resource',
            'contentMilestones',
            'quizzes' => function ($query) {
                $query->with('type')
                    ->withPivot('nb_question_affiche', 'score_success', 'use_own_questions', 'installment_month', 'opens_after_purchase_days');
            },
            'trainingCases'
        ]);

        return [
            'product'       => $product,
            'categories'    => Category::all(),
            'teachers'      => Teacher::all(),
            'countries'     => Country::all(),
            'resources'     => Resource::all(),
            'certifs'       => Certif::where('is_active', true)->orderBy('nom')->get(),
            'quizzes'       => Quiz::with('type')->get(),
            'quizTypes'     => TypeQuiz::all(),
            'trainingCases' => TrainingCase::where('is_active', true)->withCount('files')->orderBy('name')->get(),
        ];
    }

    public function duplicate(Product $product)
    {
        try {
            DB::beginTransaction();

            $product->load(['variations', 'studies', 'quizzes']);

            $newProduct = $product->replicate();
            $newProduct->image = null;
            $newProduct->iscach = 0;
            $newProduct->save();

            foreach ($product->variations as $variation) {
                $newVariation = $variation->replicate();
                $newVariation->products_id = $newProduct->id;
                $newVariation->name = $variation->name . ' (Copie)';
                $newVariation->save();
            }

            foreach ($product->studies as $study) {
                $newStudy = $study->replicate();
                $newStudy->products_id = $newProduct->id;
                $newStudy->save();
            }

            $quizIds = $product->quizzes->pluck('id')->toArray();
            if (!empty($quizIds)) {
                $newProduct->quizzes()->attach($quizIds);
            }

            DB::commit();

            return redirect()->route('products.edit', $newProduct)
                ->with('success', 'Cours dupliqué avec succès ! Vous pouvez maintenant le modifier.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la duplication du produit: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la duplication du cours: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'categories_id' => 'required|exists:categories,id',
            'period' => 'required|string|max:255',
            'point' => 'required|integer|min:0',
            'iscach' => 'boolean',
            'video' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'promo_points' => 'nullable|integer|min:0',
            'validity_months' => 'nullable|integer|min:1',
            'installment_allowed' => 'boolean',
            'teacher_id' => 'required|exists:teachers,id',
            'country_id' => 'required|exists:countries,id',
            'certif_id' => 'nullable|exists:certifs,id',
            'certificate_generation_mode' => 'nullable|in:manual,automatic',
            'type_course' => 'required|in:fa,fi,pt,autres',
            'goverrnement' => 'boolean',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'prix' => 'nullable|numeric|min:0',
            'arabic_name' => 'required|string|max:255',
            'arabic_slug' => 'required|string|max:255',
            'arabic_short_description' => 'required|string',
            'arabic_description_exams' => 'required|string',
            'arabic_description_quizzes' => 'required|string',
            'arabic_description_final_exam' => 'nullable|string',
            'arabic_description_video_exam' => 'nullable|string',
            'arabic_description_stage' => 'nullable|string',
            'arabic_description_study_case' => 'nullable|string',
            'english_name' => 'required|string|max:255',
            'english_slug' => 'required|string|max:255',
            'english_short_description' => 'required|string',
            'english_description_exams' => 'required|string',
            'english_description_quizzes' => 'required|string',
            'english_description_final_exam' => 'nullable|string',
            'english_description_video_exam' => 'nullable|string',
            'english_description_stage' => 'nullable|string',
            'english_description_study_case' => 'nullable|string',
            'quiz_ids' => 'nullable|array',
            'quiz_ids.*' => 'exists:quizzes,id',
            'quiz_nb_questions' => 'nullable|array',
            'quiz_nb_questions.*' => 'nullable|integer|min:1',
            'quiz_scores' => 'nullable|array',
            'quiz_scores.*' => 'nullable|integer|min:0|max:100',
            'quiz_installment_months' => 'nullable|array',
            'quiz_installment_months.*' => 'nullable|integer|min:1',
            'quiz_opens_after_purchase_days' => 'nullable|array',
            'quiz_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'nullable|string',
            'exam_types' => 'nullable|array',
            'exam_types.*' => 'nullable|in:theoretical,practical',
            'practical_types' => 'nullable|array',
            'practical_types.*' => 'nullable|in:online,classroom',
            'exam_nb_questions' => 'nullable|array',
            'exam_nb_questions.*' => 'nullable|integer|min:1',
            'exam_scores' => 'nullable|array',
            'exam_scores.*' => 'nullable|integer|min:0|max:100',
            'exam_installment_months' => 'nullable|array',
            'exam_installment_months.*' => 'nullable|integer|min:1',
            'exam_opens_after_purchase_days' => 'nullable|array',
            'exam_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'existing_quiz_installment_months' => 'nullable|array',
            'existing_quiz_installment_months.*' => 'nullable|integer|min:1',
            'existing_quiz_opens_after_purchase_days' => 'nullable|array',
            'existing_quiz_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'existing_exam_installment_months' => 'nullable|array',
            'existing_exam_installment_months.*' => 'nullable|integer|min:1',
            'existing_exam_opens_after_purchase_days' => 'nullable|array',
            'existing_exam_opens_after_purchase_days.*' => 'nullable|integer|min:0|max:3650',
            'content_opens_after_days' => 'nullable|array',
            'content_opens_after_days.*' => 'nullable|integer|min:0|max:3650',
            'is_stage' => 'boolean',
            'is_exam_video' => 'boolean',
            'is_classroom' => 'boolean',
            'is_zoom' => 'boolean',
            'is_online' => 'boolean',
            'study_resources' => 'nullable|array',
            'study_resources.*.resource_id' => 'required_with:study_resources|exists:resources,id',
        ]);

        $forceSave = $request->has('force_save') && $request->force_save == '1';

        if ($validator->fails()) {
            if ($forceSave) {
                Log::info('Mise à jour forcée avec erreurs de validation:', $validator->errors()->toArray());
            } else {
                if ($request->ajax()) {
                    $validationResponse = [
                        'success' => false,
                        'message' => 'Erreurs de validation',
                        'errors' => $validator->errors(),
                        'debug' => [
                            'validation_errors' => $validator->errors()->toArray(),
                            'product_id' => $product->id
                        ]
                    ];
                    Log::error('=== RÉPONSE AJAX VALIDATION (UPDATE) ===');
                    Log::error('Réponse: ' . json_encode($validationResponse));
                    Log::error('=== FIN RÉPONSE AJAX VALIDATION (UPDATE) ===');
                    return response()->json($validationResponse, 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        Log::info('Validation réussie pour la mise à jour du produit ID: ' . $product->id);

        try {
            DB::beginTransaction();

            $imageName = $product->image;

            Log::info('=== INITIALISATION IMAGE UPDATE ===');
            Log::info('Image actuelle du produit: ' . $product->image);
            Log::info('imageName initialisé à: ' . $imageName);
            Log::info('Fichier image fourni: ' . ($request->hasFile('image') ? 'OUI' : 'NON'));
            Log::info('=== FIN INITIALISATION ===');

            if ($request->hasFile('image')) {
                if ($product->image) {
                    $oldImagePath = public_path('uploads/products/images/' . $product->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $originalName = $image->getClientOriginalName();
                $extension = $image->getClientOriginalExtension();
                $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

                Log::info('=== DÉBUT MISE À JOUR IMAGE ===');
                Log::info('Nom original: ' . $originalName);
                Log::info('Extension: ' . $extension);
                Log::info('Nom sans extension: ' . $nameWithoutExtension);

                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameWithoutExtension);
                $maxNameLength = 50;
                if (strlen($cleanName) > $maxNameLength) {
                    $cleanName = substr($cleanName, 0, $maxNameLength);
                }
                $imageName = time() . '_' . $cleanName . '.' . $extension;

                Log::info('Nom nettoyé: ' . $cleanName);
                Log::info('Nom final généré: ' . $imageName);

                $imageUploadPath = public_path('uploads/products/images');
                if (!file_exists($imageUploadPath)) {
                    mkdir($imageUploadPath, 0755, true);
                }
                $image->move($imageUploadPath, $imageName);

                $fullPath = $imageUploadPath . '/' . $imageName;
                if (file_exists($fullPath)) {
                    Log::info('Fichier créé avec succès: ' . $fullPath);
                } else {
                    Log::error('ERREUR: Fichier non créé: ' . $fullPath);
                }
                Log::info('=== FIN MISE À JOUR IMAGE ===');
                Log::info('imageName après upload: ' . $imageName);
            } else {
                Log::info('Aucun fichier image fourni, conservation de l\'image actuelle: ' . $imageName);
            }

            Log::info('=== AVANT MISE À JOUR PRODUIT ===');
            Log::info('Valeur imageName à sauvegarder: ' . $imageName);
            Log::info('Type de imageName: ' . gettype($imageName));
            Log::info('Longueur de imageName: ' . strlen($imageName));
            Log::info('=== FIN AVANT MISE À JOUR ===');

            Log::info('=== AVANT MISE À JOUR PRODUIT ===');
            Log::info('ID du produit: ' . $product->id);
            Log::info('Image actuelle en base: ' . $product->image);
            Log::info('Nouvelle image à sauvegarder: ' . $imageName);
            Log::info('Fichier image fourni: ' . ($request->hasFile('image') ? 'OUI' : 'NON'));
            Log::info('=== FIN AVANT MISE À JOUR ===');

            $resolvedValidityMonths = $this->resolveValidityMonths($request);

            $updateData = [
                'iscach' => $request->has('iscach') ? 1 : 0,
                'statut' => 1,
                'categories_id' => $request->categories_id,
                'period' => $request->period,
                'point' => $request->point,
                'video' => $request->video,
                'image' => $imageName,
                'promo_points' => $request->promo_points,
                'validity_months' => $resolvedValidityMonths,
                'installment_allowed' => $request->has('installment_allowed') ? 1 : 0,
                'teacher_id' => $request->teacher_id,
                'country_id' => $request->country_id,
                'certif_id' => $request->certif_id,
                'certificate_generation_mode' => $request->certificate_generation_mode ?? 'manual',
                'type_course' => $request->type_course,
                'goverrnement' => $request->has('goverrnement') ? 1 : 0,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'prix' => $request->prix,
                'max_exam_attempts' => $request->max_exam_attempts ?? 3,
                'renewal_price' => $request->renewal_price ?? 50.00,
                'is_stage' => $request->has('is_stage') ? 1 : 0,
                'is_exam_video' => $request->has('is_exam_video') ? 1 : 0,
                'is_classroom' => $request->has('is_classroom') ? 1 : 0,
                'is_zoom' => $request->has('is_zoom') ? 1 : 0,
                'is_online' => $request->has('is_online') ? 1 : 0,
                'breuillant' => false,
                'has_practical_exam' => (!empty($request->practical_exam_type) || $request->has('has_practical_exam')) ? 1 : 0,
                'practical_exam_type' => $request->practical_exam_type,
                'is_listed' => $request->has('is_listed') ? 1 : 0,
            ];

            Log::info('Données de mise à jour: ' . json_encode($updateData));
            Log::info('validity_months from request: ' . ($request->validity_months ?? 'NULL'));
            Log::info('validity_months in updateData: ' . ($updateData['validity_months'] ?? 'NULL'));

            $result = $product->update($updateData);

            if (!empty($resolvedValidityMonths)) {
                $this->syncExistingEnrollmentExpirations($product->id, (int) $resolvedValidityMonths);
            }

            Log::info('Résultat de la mise à jour: ' . ($result ? 'SUCCÈS' : 'ÉCHEC'));
            Log::info('validity_months après update (model): ' . ($product->validity_months ?? 'NULL'));

            $dbValue = DB::table('products')->where('id', $product->id)->value('validity_months');
            Log::info('validity_months en base de données: ' . ($dbValue ?? 'NULL'));

            Log::info('=== VÉRIFICATION IMMÉDIATE ===');
            Log::info('Image après update (sans refresh): ' . $product->image);
            Log::info('imageName original: ' . $imageName);
            Log::info('Correspondance immédiate: ' . ($product->image === $imageName ? 'OUI' : 'NON'));
            Log::info('=== FIN VÉRIFICATION IMMÉDIATE ===');

            $directImageAfterUpdate = DB::table('products')->where('id', $product->id)->value('image');
            Log::info('=== VÉRIFICATION DIRECTE APRÈS UPDATE ===');
            Log::info('Image en base (direct DB après update): ' . $directImageAfterUpdate);
            Log::info('imageName original: ' . $imageName);
            Log::info('Correspondance directe: ' . ($imageName === $directImageAfterUpdate ? 'OUI' : 'NON'));
            Log::info('=== FIN VÉRIFICATION DIRECTE ===');

            $arabicVariation = $product->variations()->where('langue', 'ar')->first();
            if ($arabicVariation) {
                $arabicVariation->update([
                    'name' => $request->arabic_name,
                    'slug' => $request->arabic_slug,
                    'short_description' => $request->arabic_short_description,
                    'description_the_exams' => $request->arabic_description_exams,
                    'description_the_quizzes' => $request->arabic_description_quizzes,
                    'description_final_exam' => $request->arabic_description_final_exam,
                    'description_video_exam' => $request->arabic_description_video_exam,
                    'description_stage' => $request->arabic_description_stage,
                    'description_study_case' => $request->arabic_description_study_case,
                ]);
            }

            $englishVariation = $product->variations()->where('langue', 'en')->first();
            if ($englishVariation) {
                $englishVariation->update([
                    'name' => $request->english_name,
                    'slug' => $request->english_slug,
                    'short_description' => $request->english_short_description,
                    'description_the_exams' => $request->english_description_exams,
                    'description_the_quizzes' => $request->english_description_quizzes,
                    'description_final_exam' => $request->english_description_final_exam,
                    'description_video_exam' => $request->english_description_video_exam,
                    'description_stage' => $request->english_description_stage,
                    'description_study_case' => $request->english_description_study_case,
                ]);
            }

            $studyResourcesToCreate = [];

            Log::info('Study resources update:', [
                'study_items' => $request->input('study_items'),
                'existing_study_ids' => $request->input('existing_study_ids'),
                'existing_study_orders' => $request->input('existing_study_orders'),
                'study_resources' => $request->input('study_resources'),
            ]);

            if ($request->has('study_items') && is_array($request->study_items)) {
                foreach ($request->study_items as $order => $resourceId) {
                    if (!empty($resourceId)) {
                        $studyResourcesToCreate[$resourceId] = $order;
                    }
                }
            } elseif ($request->has('existing_study_ids') && is_array($request->existing_study_ids)) {
                $existingOrders = $request->input('existing_study_orders', []);
                foreach ($request->existing_study_ids as $index => $resourceId) {
                    $order = isset($existingOrders[$index]) ? (int)$existingOrders[$index] : $index;
                    $studyResourcesToCreate[$resourceId] = $order;
                }
            } else {
                $currentStudies = $product->studies()->get();
                foreach ($currentStudies as $study) {
                    if ($study->resource_id) {
                        $studyResourcesToCreate[$study->resource_id] = $study->order ?? 0;
                    }
                }
            }

            if ($request->has('study_resources') && is_array($request->study_resources)) {
                foreach ($request->study_resources as $studyResource) {
                    if (!empty($studyResource['resource_id'])) {
                        $order = isset($studyResource['order']) ? (int)$studyResource['order'] : count($studyResourcesToCreate);
                        $studyResourcesToCreate[$studyResource['resource_id']] = $order;
                    }
                }
            }

            if (!empty($studyResourcesToCreate) || $request->has('study_items') || $request->has('existing_study_ids') || $request->has('study_resources')) {
                $contentOpensAfterDays = $request->input('content_opens_after_days', []);
                $product->studies()->delete();
                foreach ($studyResourcesToCreate as $resourceId => $order) {
                    $resource = Resource::find($resourceId);
                    if ($resource) {
                        $opensAfter = isset($contentOpensAfterDays[$resourceId])
                            ? $this->normalizeOpensAfterPurchaseDays($contentOpensAfterDays[$resourceId])
                            : null;
                        ProductStudy::create([
                            'products_id' => $product->id,
                            'name_ar' => $resource->name_ar,
                            'name_en' => $resource->name_en,
                            'resource_id' => $resourceId,
                            'lang' => null,
                            'order' => $order,
                            'opens_after_purchase_days' => $opensAfter,
                        ]);
                    }
                }
            }

            $milestones = $request->input('content_milestones', []);
            if (!empty($milestones) && is_array($milestones)) {
                ContentMilestone::where('product_id', $product->id)->delete();
                foreach ($milestones as $resourceId => $month) {
                    $month = (int) $month;
                    if ($month > 0) {
                        $study = ProductStudy::where('products_id', $product->id)
                            ->where('resource_id', $resourceId)
                            ->first();
                        if ($study) {
                            ContentMilestone::create([
                                'product_id' => $product->id,
                                'product_study_id' => $study->id,
                                'milestone_month' => $month,
                            ]);
                        }
                    }
                }
                Log::info('Content milestones saved', [
                    'product_id' => $product->id,
                    'milestones_input' => $milestones,
                    'saved_count' => ContentMilestone::where('product_id', $product->id)->count(),
                ]);
            }

            $pivotData = [];
            $currentPivotData = $product->quizzes()->withPivot('nb_question_affiche', 'score_success', 'use_own_questions', 'installment_month', 'opens_after_purchase_days')->get()->keyBy('id');
            $installmentEnabled = $request->has('installment_allowed');
            $maxInstallmentMonth = max(1, (int) ($request->validity_months ?? 1));

            $existingQuizIds = $request->input('existing_quiz_ids', []);
            $existingQuizInstallmentMonths = $request->input('existing_quiz_installment_months', []);
            $existingQuizOpensAfterPurchase = $request->input('existing_quiz_opens_after_purchase_days', []);
            $existingQuizNbQuestions = $request->input('existing_quiz_nb_questions', []);
            $existingQuizScores = $request->input('existing_quiz_scores', []);
            if (is_array($existingQuizIds)) {
                foreach ($existingQuizIds as $quizId) {
                    $quizId = (int) $quizId;
                    if ($quizId && $currentPivotData->has($quizId)) {
                        $existingMonthRaw = isset($existingQuizInstallmentMonths[$quizId]) && $existingQuizInstallmentMonths[$quizId] !== ''
                            ? (int) $existingQuizInstallmentMonths[$quizId]
                            : (int) ($currentPivotData[$quizId]->pivot->installment_month ?? 0);
                        $existingMonth = $this->normalizeQuizInstallmentMonth(
                            $existingMonthRaw > 0 ? $existingMonthRaw : null,
                            $installmentEnabled,
                            $maxInstallmentMonth
                        );
                        if (array_key_exists($quizId, $existingQuizOpensAfterPurchase)) {
                            $opensAfter = $this->normalizeOpensAfterPurchaseDays($existingQuizOpensAfterPurchase[$quizId]);
                        } else {
                            $opensAfter = $this->normalizeOpensAfterPurchaseDays($currentPivotData[$quizId]->pivot->opens_after_purchase_days ?? null);
                        }
                        $nbQuestions = array_key_exists($quizId, $existingQuizNbQuestions) && $existingQuizNbQuestions[$quizId] !== ''
                            ? max((int) $existingQuizNbQuestions[$quizId], 1)
                            : ((int) $currentPivotData[$quizId]->pivot->nb_question_affiche ?: 10);
                        $scoreSuccess = array_key_exists($quizId, $existingQuizScores) && $existingQuizScores[$quizId] !== ''
                            ? max(min((int) $existingQuizScores[$quizId], 100), 0)
                            : ((int) $currentPivotData[$quizId]->pivot->score_success ?: 50);
                        $pivotData[$quizId] = [
                            'nb_question_affiche' => $nbQuestions,
                            'score_success' => $scoreSuccess,
                            'use_own_questions' => false,
                            'installment_month' => $existingMonth,
                            'opens_after_purchase_days' => $opensAfter,
                        ];
                    }
                }
            }

            $existingExamIds = $request->input('existing_exam_ids', []);
            $existingExamInstallmentMonths = $request->input('existing_exam_installment_months', []);
            $existingExamOpensAfterPurchase = $request->input('existing_exam_opens_after_purchase_days', []);
            $existingExamUseOwnQuestions = $request->input('existing_exam_use_own_questions', []);
            $existingExamNbQuestions = $request->input('existing_exam_nb_questions', []);
            $existingExamScores = $request->input('existing_exam_scores', []);
            $trainingCaseIds = [];

            $resolveUseOwnQuestions = static function ($rawValue): bool {
                if (is_array($rawValue)) {
                    return in_array('1', array_map('strval', $rawValue), true)
                        || in_array(1, $rawValue, true)
                        || in_array(true, $rawValue, true);
                }
                return (string) $rawValue === '1';
            };

            if (is_array($existingExamIds)) {
                foreach ($existingExamIds as $index => $examId) {
                    if (is_string($examId) && strpos($examId, 'tc_') === 0) {
                        $trainingCaseId = (int) substr($examId, 3);
                        if ($trainingCaseId > 0) {
                            $trainingCaseIds[] = $trainingCaseId;
                        }
                        continue;
                    }
                    $examId = (int) $examId;
                    $useOwnQuestions = $resolveUseOwnQuestions($existingExamUseOwnQuestions[$examId] ?? '0');
                    if ($examId && $currentPivotData->has($examId) && !isset($pivotData[$examId])) {
                        $existingMonthRaw = isset($existingExamInstallmentMonths[$examId]) && $existingExamInstallmentMonths[$examId] !== ''
                            ? (int) $existingExamInstallmentMonths[$examId]
                            : (int) ($currentPivotData[$examId]->pivot->installment_month ?? 0);
                        $existingMonth = $this->normalizeQuizInstallmentMonth(
                            $existingMonthRaw > 0 ? $existingMonthRaw : null,
                            $installmentEnabled,
                            $maxInstallmentMonth
                        );
                        if (array_key_exists($examId, $existingExamOpensAfterPurchase)) {
                            $opensAfter = $this->normalizeOpensAfterPurchaseDays($existingExamOpensAfterPurchase[$examId]);
                        } else {
                            $opensAfter = $this->normalizeOpensAfterPurchaseDays($currentPivotData[$examId]->pivot->opens_after_purchase_days ?? null);
                        }
                        $nbQuestions = array_key_exists($examId, $existingExamNbQuestions) && $existingExamNbQuestions[$examId] !== ''
                            ? max((int) $existingExamNbQuestions[$examId], 1)
                            : ((int) $currentPivotData[$examId]->pivot->nb_question_affiche ?: 10);
                        $scoreSuccess = array_key_exists($examId, $existingExamScores) && $existingExamScores[$examId] !== ''
                            ? max(min((int) $existingExamScores[$examId], 100), 0)
                            : ((int) $currentPivotData[$examId]->pivot->score_success ?: 50);
                        $pivotData[$examId] = [
                            'nb_question_affiche' => $nbQuestions,
                            'score_success' => $scoreSuccess,
                            'use_own_questions' => $useOwnQuestions,
                            'installment_month' => $existingMonth,
                            'opens_after_purchase_days' => $opensAfter,
                        ];
                    }
                }
            }

            $quizIds = $request->input('quiz_ids', []);
            $quizNbQuestions = $request->input('quiz_nb_questions', []);
            $quizScores = $request->input('quiz_scores', []);
            $quizInstallmentMonths = $request->input('quiz_installment_months', []);
            $quizOpensAfterPurchase = $request->input('quiz_opens_after_purchase_days', []);
            if (is_array($quizIds)) {
                foreach ($quizIds as $index => $quizId) {
                    $quizId = (int) $quizId;
                    if (!$quizId) {
                        continue;
                    }
                    $nbQuestions = isset($quizNbQuestions[$index]) ? (int) $quizNbQuestions[$index] : 10;
                    $scoreSuccess = isset($quizScores[$index]) ? (int) $quizScores[$index] : 50;
                    $installmentMonth = isset($quizInstallmentMonths[$index]) && $quizInstallmentMonths[$index] !== ''
                        ? (int) $quizInstallmentMonths[$index]
                        : null;
                    $installmentMonth = $this->normalizeQuizInstallmentMonth($installmentMonth, $installmentEnabled, $maxInstallmentMonth);
                    $opensAfter = isset($quizOpensAfterPurchase[$index])
                        ? $this->normalizeOpensAfterPurchaseDays($quizOpensAfterPurchase[$index])
                        : null;
                    $pivotData[$quizId] = [
                        'nb_question_affiche' => max($nbQuestions, 1),
                        'score_success' => max(min($scoreSuccess, 100), 0),
                        'use_own_questions' => false,
                        'installment_month' => $installmentMonth,
                        'opens_after_purchase_days' => $opensAfter,
                    ];
                }
            }

            $examIds = $request->input('exam_ids', []);
            $examNbQuestions = $request->input('exam_nb_questions', []);
            $examScores = $request->input('exam_scores', []);
            $examTypes = $request->input('exam_types', []);
            $examUseOwnQuestions = $request->input('exam_use_own_questions', []);
            $examInstallmentMonths = $request->input('exam_installment_months', []);
            $examOpensAfterPurchase = $request->input('exam_opens_after_purchase_days', []);
            $practicalTypes = $request->input('practical_types', []);

            $legacyTrainingCaseIds = $request->input('training_case_ids', []);
            if (is_array($legacyTrainingCaseIds)) {
                foreach ($legacyTrainingCaseIds as $legacyTrainingCaseId) {
                    $legacyTrainingCaseId = (int) $legacyTrainingCaseId;
                    if ($legacyTrainingCaseId > 0) {
                        $trainingCaseIds[] = $legacyTrainingCaseId;
                    }
                }
            }

            if (is_array($examIds)) {
                foreach ($examIds as $index => $examId) {
                    if (!$examId) {
                        continue;
                    }
                    $examType = isset($examTypes[$index]) ? $examTypes[$index] : 'theoretical';
                    $nbQuestions = isset($examNbQuestions[$index]) ? (int) $examNbQuestions[$index] : 10;
                    $scoreSuccess = isset($examScores[$index]) ? (int) $examScores[$index] : 50;

                    if ($examType === 'theoretical') {
                        $examIdInt = (int) $examId;
                        $useOwnQuestions = $resolveUseOwnQuestions($examUseOwnQuestions[$examIdInt] ?? '0');
                        $installmentMonth = isset($examInstallmentMonths[$index]) && $examInstallmentMonths[$index] !== ''
                            ? (int) $examInstallmentMonths[$index]
                            : null;
                        $installmentMonth = $this->normalizeQuizInstallmentMonth($installmentMonth, $installmentEnabled, $maxInstallmentMonth);
                        $examOpensRaw = isset($examOpensAfterPurchase[$index])
                            ? $examOpensAfterPurchase[$index]
                            : null;
                        $examOpensAfter = $this->normalizeOpensAfterPurchaseDays($examOpensRaw);
                        $pivotData[$examIdInt] = [
                            'nb_question_affiche' => max($nbQuestions, 1),
                            'score_success' => max(min($scoreSuccess, 100), 0),
                            'use_own_questions' => $useOwnQuestions,
                            'installment_month' => $installmentMonth,
                            'opens_after_purchase_days' => $examOpensAfter,
                        ];
                    } elseif ($examType === 'practical') {
                        if (strpos($examId, 'tc_') === 0) {
                            $trainingCaseId = (int) substr($examId, 3);
                            $trainingCaseIds[] = $trainingCaseId;
                            $practicalType = isset($practicalTypes[$index]) ? $practicalTypes[$index] : 'online';
                            // TODO: Store practical_type in a separate table if needed
                        }
                    }
                }
            }

            $product->quizzes()->sync($pivotData);

            $trainingCaseIds = array_values(array_unique(array_filter(array_map('intval', $trainingCaseIds))));
            $product->trainingCases()->sync($trainingCaseIds);

            $product->load('trainingCases');

            $finalHasPracticalExam = !empty($request->practical_exam_type)
                || $request->has('has_practical_exam')
                || $product->trainingCases()->count() > 0;

            $product->update([
                'has_practical_exam' => $finalHasPracticalExam ? 1 : 0,
            ]);

            Log::info('=== AVANT COMMIT ===');
            Log::info('Image avant commit: ' . $product->image);
            Log::info('imageName avant commit: ' . $imageName);
            Log::info('=== FIN AVANT COMMIT ===');

            try {
                DB::commit();
                Log::info('=== APRÈS COMMIT ===');
                Log::info('Transaction commitée avec succès');
                Log::info('=== FIN APRÈS COMMIT ===');
            } catch (\Exception $commitError) {
                Log::error('=== ERREUR COMMIT ===');
                Log::error('Erreur lors du commit: ' . $commitError->getMessage());
                Log::error('=== FIN ERREUR COMMIT ===');
                throw $commitError;
            }

            $product->refresh();

            $directImageFromDB = DB::table('products')->where('id', $product->id)->value('image');

            Log::info('=== VÉRIFICATION BASE DE DONNÉES (UPDATE) ===');
            Log::info('Nom de l\'image généré: ' . $imageName);
            Log::info('Nom de l\'image en base (après refresh): ' . $product->image);
            Log::info('Nom de l\'image en base (direct DB): ' . $directImageFromDB);
            Log::info('Correspondance (modèle): ' . ($imageName === $product->image ? 'OUI' : 'NON'));
            Log::info('Correspondance (direct DB): ' . ($imageName === $directImageFromDB ? 'OUI' : 'NON'));
            Log::info('Image a changé: ' . ($product->image !== $request->old('image') ? 'OUI' : 'NON'));
            Log::info('=== FIN VÉRIFICATION (UPDATE) ===');

            if ($request->ajax()) {
                $response = [
                    'success' => true,
                    'message' => $forceSave
                        ? 'Produit mis à jour en mode brouillon ! ID: ' . $product->id
                        : 'Produit mis à jour avec succès ! ID: ' . $product->id,
                    'redirect' => route('products.index'),
                    'breuillant' => false,
                    'debug' => [
                        'image_generated' => $imageName,
                        'image_in_db' => $product->image,
                        'image_match' => ($imageName === $product->image),
                        'product_id' => $product->id
                    ]
                ];

                Log::info('=== RÉPONSE AJAX (UPDATE) ===');
                Log::info('Réponse: ' . json_encode($response));
                Log::info('=== FIN RÉPONSE AJAX (UPDATE) ===');

                return response()->json($response);
            }

            return redirect()->route('products.index')
                ->with('success', 'Produit mis à jour avec succès ! ID: ' . $product->id);

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->ajax()) {
                $errorResponse = [
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du produit: ' . $e->getMessage(),
                    'debug' => [
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'product_id' => isset($product) ? $product->id : 'N/A'
                    ]
                ];

                Log::error('=== RÉPONSE AJAX ERREUR (UPDATE) ===');
                Log::error('Réponse: ' . json_encode($errorResponse));
                Log::error('=== FIN RÉPONSE AJAX ERREUR (UPDATE) ===');

                return response()->json($errorResponse, 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour du produit: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            $product->variations()->delete();
            $product->types()->delete();
            $product->studies()->delete();
            $product->delete();
            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produit supprimé avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression du produit: ' . $e->getMessage()]);
        }
    }

    public function publicArabic()
    {
        return Product::with(['variations' => function ($query) {
            $query->where('langue', 'ar');
        }, 'category', 'teacher', 'country'])
        ->whereHas('variations', function ($query) {
            $query->where('langue', 'ar');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12);
    }

    public function publicEnglish()
    {
        return Product::with(['variations' => function ($query) {
            $query->where('langue', 'en');
        }, 'category', 'teacher', 'country'])
        ->whereHas('variations', function ($query) {
            $query->where('langue', 'en');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12);
    }

    public function removeStudyResource(Request $request, Product $product)
    {
        try {
            $resourceId = $request->input('resource_id');

            if (!$resourceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource ID is required'
                ], 400);
            }

            $deleted = ProductStudy::where('products_id', $product->id)
                ->where('resource_id', $resourceId)
                ->delete();

            if ($deleted) {
                Log::info("Removed resource {$resourceId} from product {$product->id}");
                return response()->json([
                    'success' => true,
                    'message' => 'Ressource retirée du cours avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found in this product'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error removing study resource: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}
