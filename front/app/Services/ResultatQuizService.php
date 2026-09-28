<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Quiz;
use App\Models\ResultatQuiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultatQuizService
{
    public function index(Request $request)
    {
        $query = ResultatQuiz::with(['student', 'product', 'quiz']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->filled('success')) {
            $query->where('success', $request->success);
        }

        $resultats = $query->orderBy('created_at', 'desc')->paginate(15);
        $students = User::all();
        $products = Product::all();
        $quizzes = Quiz::all();

        return view('resultat-quizzes.index', compact('resultats', 'students', 'products', 'quizzes'));
    }

    public function create()
    {
        $students = User::all();
        $products = Product::all();
        $quizzes = Quiz::all();

        return view('resultat-quizzes.create', compact('students', 'products', 'quizzes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'score' => 'required|numeric|min:0|max:100',
            'success' => 'boolean',
            'attempts' => 'integer|min:0',
        ]);

        $existingResult = ResultatQuiz::where('student_id', $request->student_id)
            ->where('quiz_id', $request->quiz_id)
            ->first();

        if ($existingResult) {
            return back()->withErrors(['error' => 'Un résultat existe déjà pour cet étudiant et ce quiz.']);
        }

        try {
            ResultatQuiz::create($request->all());

            return redirect()->route('resultat-quizzes.index')
                ->with('success', 'Résultat créé avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function show(ResultatQuiz $resultatQuiz)
    {
        $resultatQuiz->load(['student', 'product', 'quiz']);

        return view('resultat-quizzes.show', compact('resultatQuiz'));
    }

    public function edit(ResultatQuiz $resultatQuiz)
    {
        $students = User::all();
        $products = Product::all();
        $quizzes = Quiz::all();

        return view('resultat-quizzes.edit', compact('resultatQuiz', 'students', 'products', 'quizzes'));
    }

    public function update(Request $request, ResultatQuiz $resultatQuiz)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'score' => 'required|numeric|min:0|max:100',
            'success' => 'boolean',
            'attempts' => 'integer|min:0',
        ]);

        try {
            $resultatQuiz->update($request->all());

            return redirect()->route('resultat-quizzes.index')
                ->with('success', 'Résultat mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(ResultatQuiz $resultatQuiz)
    {
        try {
            $resultatQuiz->delete();

            return redirect()->route('resultat-quizzes.index')
                ->with('success', 'Résultat supprimé avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function startQuiz(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        $studentId = Auth::id();

        $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked((int) $request->product_id, app()->getLocale());
        if ($blockResponse) {
            return $blockResponse;
        }

        $hasAccess = Product::find($request->product_id)->hasStudentAccess($studentId);
        if (!$hasAccess) {
            return back()->withErrors(['error' => 'Vous devez acheter ce cours pour accéder aux quiz.']);
        }

        $productEntity = Product::find($request->product_id);
        $quizEntity = Quiz::find($request->quiz_id);
        if ($productEntity && $quizEntity) {
            $blockReason = app(InstallmentService::class)->getQuizAccessBlockReason($productEntity, $quizEntity, $studentId);
            if ($blockReason !== null) {
                return back()->withErrors(['error' => __('messages.content_locked')]);
            }
        }

        $canTakeQuiz = Product::find($request->product_id)->canStudentTakeQuiz($studentId, $request->quiz_id);
        if (!$canTakeQuiz) {
            return back()->withErrors(['error' => 'Vous ne pouvez plus passer ce quiz.']);
        }

        $isBlocked = Product::find($request->product_id)->isStudentBlockedFromQuiz($studentId, $request->quiz_id);
        if ($isBlocked) {
            return back()->withErrors(['error' => 'Vous êtes bloqué de ce quiz.']);
        }

        try {
            $resultat = ResultatQuiz::where('student_id', $studentId)
                ->where('quiz_id', $request->quiz_id)
                ->first();

            if ($resultat) {
                $resultat->update([
                    'product_id' => $request->product_id,
                    'score' => 0,
                    'success' => false,
                    'attempts' => $resultat->attempts + 1,
                    'started_at' => now(),
                    'completed_at' => null,
                ]);
            } else {
                $resultat = ResultatQuiz::create([
                    'student_id' => $studentId,
                    'product_id' => $request->product_id,
                    'quiz_id' => $request->quiz_id,
                    'score' => 0,
                    'success' => false,
                    'attempts' => 1,
                    'started_at' => now(),
                ]);
            }

            return redirect()->route('quiz.take', [
                'product_id' => $request->product_id,
                'quiz_id' => $request->quiz_id,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors du démarrage du quiz: ' . $e->getMessage()]);
        }
    }

    public function submitQuiz(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.response_id' => 'required|exists:reponse_questions,id',
        ]);

        $studentId = Auth::id();

        $submitProduct = Product::find($request->product_id);
        $submitQuizEntity = Quiz::find($request->quiz_id);
        if ($submitProduct && $submitQuizEntity) {
            $submitBlockReason = app(InstallmentService::class)->getQuizAccessBlockReason($submitProduct, $submitQuizEntity, $studentId);
            if ($submitBlockReason !== null) {
                return back()->withErrors(['error' => __('messages.content_locked')]);
            }
        }

        try {
            DB::beginTransaction();

            $resultat = ResultatQuiz::where('student_id', $studentId)
                ->where('product_id', $request->product_id)
                ->where('quiz_id', $request->quiz_id)
                ->first();

            if (!$resultat) {
                throw new \Exception('Aucun quiz en cours trouvé.');
            }

            $totalQuestions = count($request->answers);
            $correctAnswers = 0;

            foreach ($request->answers as $answer) {
                $isCorrect = DB::table('reponse_questions')
                    ->where('id', $answer['response_id'])
                    ->where('question_id', $answer['question_id'])
                    ->value('is_correcte');

                if ($isCorrect) {
                    $correctAnswers++;
                }

                DB::table('response_quizzes')->insert([
                    'product_id' => $request->product_id,
                    'student_id' => $studentId,
                    'quiz_id' => $request->quiz_id,
                    'question_id' => $answer['question_id'],
                    'response_id' => $answer['response_id'],
                    'is_correct' => $isCorrect,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $score = ($correctAnswers / $totalQuestions) * 100;
            $success = $score >= 50;

            $resultat->update([
                'score' => $score,
                'success' => $success,
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('quiz.result', $resultat->id)
                ->with('success', 'Quiz terminé !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la soumission: ' . $e->getMessage()]);
        }
    }

    public function showResult(ResultatQuiz $resultatQuiz)
    {
        if (Auth::id() !== $resultatQuiz->student_id) {
            abort(403);
        }

        $resultatQuiz->load(['product', 'quiz']);

        return view('quiz.result', compact('resultatQuiz'));
    }

    public function adminUpdateSuccess(Request $request, ResultatQuiz $resultatQuiz)
    {
        $request->validate([
            'success' => 'required|boolean',
        ]);

        try {
            $resultatQuiz->update([
                'success' => $request->success,
                'completed_at' => $request->success ? now() : null,
            ]);

            return back()->with('success', 'Statut de succès mis à jour !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function resetAttempts(ResultatQuiz $resultatQuiz)
    {
        try {
            $resultatQuiz->update([
                'attempts' => 0,
                'success' => false,
                'score' => 0,
                'started_at' => null,
                'completed_at' => null,
            ]);

            return back()->with('success', 'Tentatives réinitialisées !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la réinitialisation: ' . $e->getMessage()]);
        }
    }
}
