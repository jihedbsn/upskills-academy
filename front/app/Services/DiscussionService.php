<?php

namespace App\Services;

use App\Mail\AdminNewCommentNotification;
use App\Models\Discussion;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DiscussionService
{
    public function index(Request $request)
    {
        $query = Discussion::with(['student', 'product']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        $discussions = $query->orderBy('created_at', 'desc')->paginate(15);
        [$products, $students] = $this->getProductsAndStudents();

        return view('discussions.index', compact('discussions', 'products', 'students'));
    }

    public function create()
    {
        [$products, $students] = $this->getProductsAndStudents();

        return view('discussions.create', compact('products', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'student_id' => 'required|exists:students,id',
            'commentaire' => 'required|string|max:1000',
        ]);

        $product = Product::find($request->product_id);
        $hasPurchased = $product ? $product->hasStudentAccess($request->student_id) : false;
        if (!$hasPurchased) {
            return back()->withErrors(['error' => 'L\'étudiant doit avoir acheté le cours pour commenter.']);
        }

        try {
            $discussion = Discussion::create($request->all());
            $this->sendAdminCommentEmail($discussion);

            return redirect()->route('discussions.index')
                ->with('success', 'Discussion créée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['student', 'product', 'responses.admin']);

        return view('discussions.show', compact('discussion'));
    }

    public function edit(Discussion $discussion)
    {
        [$products, $students] = $this->getProductsAndStudents();

        return view('discussions.edit', compact('discussion', 'products', 'students'));
    }

    public function update(Request $request, Discussion $discussion)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'student_id' => 'required|exists:students,id',
            'commentaire' => 'required|string|max:1000',
        ]);

        try {
            $discussion->update($request->all());

            return redirect()->route('discussions.index')
                ->with('success', 'Discussion mise à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(Discussion $discussion)
    {
        try {
            $discussion->delete();

            return redirect()->route('discussions.index')
                ->with('success', 'Discussion supprimée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function showProductDiscussions($productId)
    {
        $product = Product::findOrFail($productId);
        $discussions = $product->getApprovedDiscussions()->paginate(10);
        $hasPurchased = Auth::check() ? $product->hasStudentAccess(Auth::id()) : false;

        return view('discussions.product', compact('product', 'discussions', 'hasPurchased'));
    }

    public function storeStudentComment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'commentaire' => 'required|string|max:1000',
        ]);

        $studentId = Auth::id();
        $product = Product::find($request->product_id);
        $hasPurchased = $product ? $product->hasStudentAccess($studentId) : false;

        if (!$hasPurchased) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez acheter ce cours pour commenter.',
                ], 403);
            }

            return back()->withErrors(['error' => 'Vous devez acheter ce cours pour commenter.']);
        }

        try {
            $discussion = Discussion::create([
                'product_id' => $request->product_id,
                'student_id' => $studentId,
                'commentaire' => $request->commentaire,
                'is_approved' => false,
            ]);

            $this->sendAdminCommentEmail($discussion);

            try {
                $student = Auth::user();
                if ($student && $product) {
                    Notification::notifyAllAdmins(
                        Notification::TYPE_COMMENT,
                        'New Discussion',
                        $student->first_name . ' ' . $student->last_name . ' posted a new discussion on "' . ($product->name_en ?? $product->name_ar) . '"',
                        config('app.admin_url') . '/discussions/' . $discussion->id,
                        ['discussion_id' => $discussion->id, 'student_id' => $studentId, 'product_id' => $product->id],
                        '💬',
                        'blue'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Admin comment notification creation error: ' . $e->getMessage());
            }

            $discussion->load('student');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commentaire soumis et en attente d\'approbation !',
                    'discussion' => [
                        'id' => $discussion->id,
                        'commentaire' => $discussion->commentaire,
                        'student_name' => $discussion->student ? $discussion->student->full_name : 'Utilisateur',
                        'student_image' => $discussion->student ? $discussion->student->image : null,
                        'created_at' => $discussion->created_at->format('d/m/Y H:i'),
                        'is_approved' => $discussion->is_approved,
                    ],
                ]);
            }

            return back()->with('success', 'Commentaire soumis et en attente d\'approbation !');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la soumission: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Erreur lors de la soumission: ' . $e->getMessage()]);
        }
    }

    public function approve(Discussion $discussion)
    {
        try {
            $discussion->approve();

            return back()->with('success', 'Discussion approuvée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'approbation: ' . $e->getMessage()]);
        }
    }

    public function disapprove(Discussion $discussion)
    {
        try {
            $discussion->disapprove();

            return back()->with('success', 'Discussion désapprouvée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la désapprobation: ' . $e->getMessage()]);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'discussion_ids' => 'required|array',
            'discussion_ids.*' => 'exists:discussions,id',
        ]);

        try {
            Discussion::whereIn('id', $request->discussion_ids)->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Discussions approuvées en masse !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'approbation en masse: ' . $e->getMessage()]);
        }
    }

    private function getProductsAndStudents(): array
    {
        return [Product::all(), Student::all()];
    }

    private function sendAdminCommentEmail(Discussion $discussion): void
    {
        try {
            Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                new AdminNewCommentNotification($discussion)
            );
        } catch (\Exception $e) {
            Log::error('Admin comment notification email error: ' . $e->getMessage());
        }
    }
}
