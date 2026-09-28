<?php

namespace App\Services;

use App\Models\Discussion;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ResponseDiscussion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscussionService
{
    public function index(Request $request)
    {
        $query = Discussion::with(['student', 'product', 'responses']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
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

        if ($request->filled('has_responses')) {
            if ($request->has_responses) {
                $query->has('responses');
            } else {
                $query->doesntHave('responses');
            }
        }

        $discussions = $query->orderBy('created_at', 'desc')->paginate(15);
        $products = Product::all();

        $stats = [
            'total' => Discussion::count(),
            'approved' => Discussion::where('is_approved', true)->count(),
            'pending' => Discussion::where('is_approved', false)->count(),
            'total_responses' => ResponseDiscussion::count(),
        ];

        return view('discussions.index', compact('discussions', 'products', 'stats'));
    }

    public function create()
    {
        $students = Student::all();
        $products = Product::all();

        return view('discussions.create', compact('students', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'commentaire' => 'required|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $discussion = Discussion::create([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'commentaire' => $request->commentaire,
            'is_approved' => $request->is_approved ?? false,
        ]);

        $student = Student::find($request->student_id);
        $product = Product::find($request->product_id);
        $studentName = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';
        $productName = $product ? $product->title : 'Unknown Course';

        Notification::notifyAllAdmins(
            Notification::TYPE_COMMENT,
            'New Discussion',
            "New discussion from {$studentName} on {$productName}",
            route('admin.discussions.show', $discussion->id),
            ['discussion_id' => $discussion->id],
            '💬',
            'blue'
        );

        return redirect()->route('admin.discussions.index')->with('success', 'Discussion créée avec succès.');
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['student', 'product', 'responses.admin']);
        return view('discussions.show', compact('discussion'));
    }

    public function edit(Discussion $discussion)
    {
        $students = Student::all();
        $products = Product::all();

        return view('discussions.edit', compact('discussion', 'students', 'products'));
    }

    public function update(Request $request, Discussion $discussion)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'commentaire' => 'required|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        $hasAccess = DB::table('product_students')
            ->where('product_id', $request->product_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Cet étudiant n\'a pas accès à ce cours.']);
        }

        $discussion->update([
            'student_id' => $request->student_id,
            'product_id' => $request->product_id,
            'commentaire' => $request->commentaire,
            'is_approved' => $request->is_approved ?? false,
        ]);

        return redirect()->route('admin.discussions.index')->with('success', 'Discussion mise à jour avec succès.');
    }

    public function destroy(Discussion $discussion)
    {
        try {
            $discussion->responses()->delete();
            $discussion->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.discussions.index')->with('success', 'Discussion supprimée avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
            }
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function approve(Discussion $discussion)
    {
        try {
            $discussion->update(['is_approved' => true]);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.discussions.index')->with('success', 'Discussion approuvée avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'approbation.']);
            }
            return back()->with('error', 'Erreur lors de l\'approbation.');
        }
    }

    public function disapprove(Discussion $discussion)
    {
        try {
            $discussion->update(['is_approved' => false]);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.discussions.index')->with('success', 'Discussion désapprouvée avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la désapprobation.']);
            }
            return back()->with('error', 'Erreur lors de la désapprobation.');
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'discussion_ids' => 'required|array',
            'discussion_ids.*' => 'exists:discussions,id',
        ]);

        try {
            Discussion::whereIn('id', $request->discussion_ids)
                ->update(['is_approved' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'approbation en lot.']);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'discussion_ids' => 'required|array',
            'discussion_ids.*' => 'exists:discussions,id',
        ]);

        try {
            Discussion::whereIn('id', $request->discussion_ids)->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression en lot.']);
        }
    }
}
