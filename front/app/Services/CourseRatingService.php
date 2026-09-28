<?php

namespace App\Services;

use App\Mail\AdminNewRatingNotification;
use App\Models\CourseRating;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CourseRatingService
{
    public function index(Request $request)
    {
        $query = CourseRating::with(['student', 'product']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $ratings = $query->orderBy('created_at', 'desc')->paginate(15);
        [$products, $students] = $this->getProductsAndStudents();

        return view('course-ratings.index', compact('ratings', 'products', 'students'));
    }

    public function create()
    {
        [$products, $students] = $this->getProductsAndStudents();

        return view('course-ratings.create', compact('products', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'student_id' => 'required|exists:students,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $student = Student::find($request->student_id);
        $canRate = $student?->canRateProduct($request->product_id);
        if (!$canRate) {
            return back()->withErrors(['error' => 'L\'étudiant doit avoir acheté le cours pour le noter.']);
        }

        $existingRating = CourseRating::where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if ($existingRating) {
            return back()->withErrors(['error' => 'Cet étudiant a déjà noté ce cours.']);
        }

        try {
            $data = $request->only(['product_id', 'student_id', 'rating', 'commentaire']);
            $data['is_approved'] = true;
            $data['approved_at'] = now();

            $rating = CourseRating::create($data);

            $product = Product::find($request->product_id);
            $this->notifyAdminsAboutRating($rating, $student, $product);

            return redirect()->route('course-ratings.index')
                ->with('success', 'Rating créé et publié automatiquement.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function show(CourseRating $courseRating)
    {
        $courseRating->load(['student', 'product']);

        return view('course-ratings.show', compact('courseRating'));
    }

    public function edit(CourseRating $courseRating)
    {
        [$products, $students] = $this->getProductsAndStudents();

        return view('course-ratings.edit', compact('courseRating', 'products', 'students'));
    }

    public function update(Request $request, CourseRating $courseRating)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'student_id' => 'required|exists:students,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        try {
            $courseRating->update($request->all());

            return redirect()->route('course-ratings.index')
                ->with('success', 'Rating mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(CourseRating $courseRating)
    {
        try {
            $courseRating->delete();

            return redirect()->route('course-ratings.index')
                ->with('success', 'Rating supprimé avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function showProductRatings($productId)
    {
        $product = Product::findOrFail($productId);
        $ratings = $product->getApprovedRatings()->paginate(10);
        $canRate = false;

        if (Auth::check()) {
            $student = Student::find(Auth::id());
            $canRate = $student ? $student->canRateProduct($productId) : false;
        }

        $averageRating = $product->getAverageRating();
        $ratingsCount = $product->getRatingsCount();

        return view('course-ratings.product', compact('product', 'ratings', 'canRate', 'averageRating', 'ratingsCount'));
    }

    public function storeStudentRating(Request $request)
    {
        Log::info('Rating submission data', [
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'rating_type' => gettype($request->rating),
            'commentaire' => $request->commentaire,
            'commentaire_type' => gettype($request->commentaire),
            'commentaire_length' => $request->commentaire ? strlen($request->commentaire) : 0,
            'all_data' => $request->all(),
            'raw_input' => $request->input('commentaire'),
            'has_commentaire' => $request->has('commentaire'),
            'filled_commentaire' => $request->filled('commentaire'),
        ]);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $studentId = Auth::id();
        $student = Student::find($studentId);
        $canRate = $student?->canRateProduct($request->product_id);

        if (!$canRate) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez avoir réussi ce cours pour le noter.',
                ]);
            }

            return back()->withErrors(['error' => 'Vous devez avoir réussi ce cours pour le noter.']);
        }

        $existingRating = CourseRating::where('product_id', $request->product_id)
            ->where('student_id', $studentId)
            ->first();

        if ($existingRating) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà noté ce cours. Vous ne pouvez noter qu\'une seule fois.',
                ]);
            }

            return back()->withErrors(['error' => 'Vous avez déjà noté ce cours. Vous ne pouvez noter qu\'une seule fois.']);
        }

        try {
            $dataToSave = [
                'product_id' => $request->product_id,
                'student_id' => $studentId,
                'rating' => $request->rating,
                'commentaire' => $request->commentaire ?: null,
                'is_approved' => true,
                'approved_at' => now(),
            ];
            Log::info('Creating rating with data', $dataToSave);

            $rating = CourseRating::create($dataToSave);

            Log::info('Rating created', [
                'id' => $rating->id,
                'rating_value' => $rating->rating,
                'commentaire' => $rating->commentaire,
                'commentaire_from_request' => $request->commentaire,
                'fresh_rating' => $rating->fresh()->toArray(),
            ]);

            $product = Product::find($request->product_id);
            $this->notifyAdminsAboutRating($rating, $student, $product);

            $rating->load('student');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rating soumis avec succès !',
                    'rating' => [
                        'id' => $rating->id,
                        'rating' => $rating->rating,
                        'commentaire' => $rating->commentaire,
                        'created_at' => $rating->created_at->format('d/m/Y'),
                        'student_name' => $rating->student ? $rating->student->full_name : 'Utilisateur',
                        'student_image' => $rating->student ? $rating->student->image : null,
                        'is_approved' => $rating->is_approved,
                    ],
                ]);
            }

            return back()->with('success', 'Rating soumis et publié automatiquement.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la soumission: ' . $e->getMessage(),
                ]);
            }

            return back()->withErrors(['error' => 'Erreur lors de la soumission: ' . $e->getMessage()]);
        }
    }

    public function approve(CourseRating $courseRating)
    {
        try {
            $courseRating->approve();

            return back()->with('success', 'Rating approuvé !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'approbation: ' . $e->getMessage()]);
        }
    }

    public function disapprove(CourseRating $courseRating)
    {
        try {
            $courseRating->disapprove();

            return back()->with('success', 'Rating désapprouvé !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la désapprobation: ' . $e->getMessage()]);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'rating_ids' => 'required|array',
            'rating_ids.*' => 'exists:course_ratings,id',
        ]);

        try {
            CourseRating::whereIn('id', $request->rating_ids)->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Ratings approuvés en masse !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'approbation en masse: ' . $e->getMessage()]);
        }
    }

    public function statistics()
    {
        $totalRatings = CourseRating::count();
        $approvedRatings = CourseRating::approved()->count();
        $pendingRatings = CourseRating::where('is_approved', false)->count();
        $averageRating = CourseRating::approved()->avg('rating') ?? 0;

        $ratingDistribution = CourseRating::approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $topRatedProducts = Product::withCount(['courseRatings as ratings_count' => function ($query) {
            $query->approved();
        }])
            ->withAvg(['courseRatings as average_rating' => function ($query) {
                $query->approved();
            }], 'rating')
            ->having('ratings_count', '>', 0)
            ->orderByDesc('average_rating')
            ->limit(10)
            ->get();

        return view('course-ratings.statistics', compact(
            'totalRatings',
            'approvedRatings',
            'pendingRatings',
            'averageRating',
            'ratingDistribution',
            'topRatedProducts'
        ));
    }

    private function getProductsAndStudents(): array
    {
        return [Product::all(), Student::all()];
    }

    private function notifyAdminsAboutRating(CourseRating $rating, ?Student $student, ?Product $product): void
    {
        try {
            Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                new AdminNewRatingNotification($rating)
            );
        } catch (\Exception $e) {
            Log::error('Admin rating notification email error: ' . $e->getMessage());
        }

        try {
            if (!$student || !$product) {
                return;
            }

            Notification::notifyAllAdmins(
                Notification::TYPE_RATING,
                'New Course Rating',
                $student->first_name . ' ' . $student->last_name . ' rated "' . ($product->name_en ?? $product->name_ar) . '" with ' . $rating->rating . ' stars',
                config('app.admin_url') . '/course-ratings/' . $rating->id,
                [
                    'rating_id' => $rating->id,
                    'rating_value' => $rating->rating,
                    'student_id' => $student->id,
                    'product_id' => $product->id,
                ],
                '⭐',
                'yellow'
            );
        } catch (\Exception $e) {
            Log::error('Admin rating notification creation error: ' . $e->getMessage());
        }
    }
}
