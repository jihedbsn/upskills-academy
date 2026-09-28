<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Quiz;
use App\Models\TypeQuiz;
use Illuminate\Http\Request;

class ProductQuizService
{
    public function index(Product $product): array
    {
        return [
            'product'          => $product,
            'productQuizzes'   => $product->quizzes()->with('type')->get(),
            'quizTypes'        => TypeQuiz::all(),
            'availableQuizzes' => Quiz::with('type')->get(),
        ];
    }

    public function store(Request $request, Product $product): void
    {
        $request->validate([
            'quiz_ids'   => 'required|array',
            'quiz_ids.*' => 'exists:quizzes,id',
            'installment_months' => 'nullable|array',
            'installment_months.*' => 'nullable|integer|min:1',
        ]);

        $installmentAllowed = (bool) $product->installment_allowed;
        $maxMonth = max(1, (int) ($product->validity_months ?? 1));

        $attachPayload = [];
        foreach ($request->quiz_ids as $quizId) {
            $month = $request->input("installment_months.{$quizId}");
            $month = is_null($month) || $month === '' ? null : (int) $month;

            if ($installmentAllowed) {
                if (is_null($month)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "installment_months.{$quizId}" => 'Please select installment month for each selected quiz.',
                    ]);
                }
                if ($month < 1 || $month > $maxMonth) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "installment_months.{$quizId}" => "Installment month must be between 1 and {$maxMonth}.",
                    ]);
                }
            }

            $attachPayload[$quizId] = [
                'installment_month' => $installmentAllowed ? $month : null,
            ];
        }

        $product->quizzes()->syncWithoutDetaching($attachPayload);
    }

    public function updateInstallmentMonth(Request $request, Product $product, Quiz $quiz): void
    {
        $request->validate([
            'installment_month' => 'required|integer|min:1',
        ]);

        $maxMonth = max(1, (int) ($product->validity_months ?? 1));
        $month = (int) $request->installment_month;
        if ($month > $maxMonth) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'installment_month' => "Installment month must be between 1 and {$maxMonth}.",
            ]);
        }

        $product->quizzes()->updateExistingPivot($quiz->id, [
            'installment_month' => $month,
        ]);
    }

    public function destroy(Product $product, Quiz $quiz)
    {
        try {
            $product->quizzes()->detach($quiz->id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quiz supprimé du produit avec succès.'
                ]);
            }

            return redirect()->back()->with('success', 'Quiz supprimé du produit avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de la suppression du quiz.');
        }
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        $type   = $request->get('type');

        $query = Quiz::with('type');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('titre', 'like', "%{$type}%");
            });
        }

        return $query->get();
    }
}
