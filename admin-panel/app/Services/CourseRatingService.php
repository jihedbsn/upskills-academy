<?php

namespace App\Services;

use App\Models\CourseRating;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseRatingService
{
    public function index(Request $request)
    {
        $query = CourseRating::with(['student', 'product']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->student . '%')
                    ->orWhere('last_name', 'like', '%' . $request->student . '%')
                    ->orWhere('email', 'like', '%' . $request->student . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $ratings = $query->orderBy('created_at', 'desc')->paginate(15);
        $products = Product::all();

        $stats = [
            'total' => CourseRating::count(),
            'approved' => CourseRating::where('is_approved', true)->count(),
            'pending' => CourseRating::where('is_approved', false)->count(),
            'average_rating' => CourseRating::where('is_approved', true)->avg('rating') ?? 0,
            'rating_distribution' => CourseRating::where('is_approved', true)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return view('course-ratings.index', compact('ratings', 'products', 'stats'));
    }

    public function create()
    {
        $students = Student::all();
        $products = Product::all();

        return view('course-ratings.create', compact('students', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $existingRating = CourseRating::where('student_id', $request->student_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingRating) {
            return back()->withErrors(['error' => 'Cet étudiant a déjà évalué ce cours.']);
        }

        $rating = CourseRating::create([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'commentaire' => $request->commentaire,
            'is_approved' => $request->is_approved ?? false,
        ]);

        $student = Student::find($request->student_id);
        $product = Product::find($request->product_id);
        $studentName = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';
        $productName = $product ? $product->title : 'Unknown Course';

        Notification::notifyAllAdmins(
            Notification::TYPE_RATING,
            'New Course Rating',
            "{$studentName} rated {$productName} ({$request->rating}/5 stars)",
            route('admin.course-ratings.show', $rating->id),
            ['rating_id' => $rating->id, 'rating' => $request->rating],
            '⭐',
            'orange'
        );

        return redirect()->route('admin.course-ratings.index')->with('success', 'Évaluation créée avec succès.');
    }

    public function show(CourseRating $courseRating)
    {
        $courseRating->load(['student', 'product']);

        return view('course-ratings.show', compact('courseRating'));
    }

    public function respond(Request $request, CourseRating $courseRating)
    {
        $request->validate([
            'admin_response' => 'required|string|max:2000',
        ]);

        try {
            $courseRating->update([
                'admin_response' => $request->admin_response,
                'admin_response_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully.',
                'admin_response' => $courseRating->admin_response,
                'admin_response_at' => $courseRating->admin_response_at->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(CourseRating $courseRating)
    {
        $students = Student::all();
        $products = Product::all();

        return view('course-ratings.edit', compact('courseRating', 'students', 'products'));
    }

    public function update(Request $request, CourseRating $courseRating)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $courseRating->update([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'commentaire' => $request->commentaire,
            'is_approved' => $request->is_approved ?? false,
        ]);

        return redirect()->route('admin.course-ratings.index')->with('success', 'Évaluation mise à jour avec succès.');
    }

    public function destroy(CourseRating $courseRating)
    {
        try {
            $courseRating->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
        }
    }

    public function approve(CourseRating $courseRating)
    {
        try {
            $courseRating->update(['is_approved' => true]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'approbation.']);
        }
    }

    public function disapprove(CourseRating $courseRating)
    {
        try {
            $courseRating->update(['is_approved' => false]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la désapprobation.']);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'rating_ids' => 'required|array',
            'rating_ids.*' => 'exists:course_ratings,id',
        ]);

        try {
            CourseRating::whereIn('id', $request->rating_ids)
                ->update(['is_approved' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'approbation en lot.']);
        }
    }
}
