<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\EmailLog;
use App\Models\CustomerPoint;
use App\Models\BlockStudentCourse;
use App\Models\Order;
use App\Models\Product;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountBlocked;
use Illuminate\Support\Facades\Http;


class StudentController extends Controller
{

    public function index(Request $request)
    {
        $query = Student::query();

        // Filter by name if search parameter is provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('country', 'like', '%' . $searchTerm . '%');
            });
        }

        // Paginate results (10 items per page)
        $students = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'country' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
        ]);

        $data = $request->all();

        // Hash password
        $data['password'] = Hash::make($request->password);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/students'), $imageName);
            $data['image'] = $imageName;
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully!');
    }

    public function show(Student $student)
    {
        // Charger les relations et statistiques de l'étudiant
        $student->load([
            'orders' => function($query) {
                $query->where('payment_success', 1)->with('product.variations');
            }
        ]);

        // Statistiques des cours
        $enrolledCourses = $student->orders()
            ->where('payment_success', 1)
            ->whereNotNull('product_id')
            ->where('product_id', '>', 0)
            ->with('product.variations')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques des quiz
        $quizStats = [
            'total_attempts' => 0,
            'successful_attempts' => 0,
            'failed_attempts' => 0,
            'average_score' => 0,
            'best_score' => 0,
            'latest_quiz' => null
        ];

        // Vérifier si la table historique_quizzes existe et a des données
        try {
            if (Schema::hasTable('historique_quizzes')) {
                $quizHistory = \DB::table('historique_quizzes')
                    ->where('student_id', $student->id)
                    ->get();

                if ($quizHistory->count() > 0) {
                    $quizStats['total_attempts'] = $quizHistory->count();
                    $quizStats['successful_attempts'] = $quizHistory->where('success', true)->count();
                    $quizStats['failed_attempts'] = $quizHistory->where('success', false)->count();
                    $quizStats['average_score'] = round($quizHistory->avg('score'), 2);
                    $quizStats['best_score'] = $quizHistory->max('score');

                    $latestQuiz = $quizHistory->sortByDesc('completed_at')->first();
                    if ($latestQuiz) {
                        $quizStats['latest_quiz'] = $latestQuiz;
                    }
                }
            }
        } catch (\Exception $e) {
            // Si erreur avec les quiz, on continue sans ces stats
            \Log::info('Quiz stats not available for student: ' . $e->getMessage());
        }

        // Statistiques des stages
        $stageStats = [
            'total_submissions' => 0,
            'validated' => 0,
            'rejected' => 0,
            'pending' => 0,
            'latest_submission' => null
        ];

        try {
            if (Schema::hasTable('student_stage_courses')) {
                $stages = \DB::table('student_stage_courses')
                    ->where('student_id', $student->id)
                    ->get();

                if ($stages->count() > 0) {
                    $stageStats['total_submissions'] = $stages->count();
                    $stageStats['validated'] = $stages->where('is_valid', 1)->count();
                    $stageStats['rejected'] = $stages->where('is_valid', -1)->count();
                    $stageStats['pending'] = $stages->where('is_valid', 0)->count();

                    $latestStage = $stages->sortByDesc('submitted_at')->first();
                    if ($latestStage) {
                        $stageStats['latest_submission'] = $latestStage;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info('Stage stats not available for student: ' . $e->getMessage());
        }

        // Statistiques des examens vidéo
        $videoExamStats = [
            'total_submissions' => 0,
            'validated' => 0,
            'rejected' => 0,
            'pending' => 0,
            'latest_submission' => null
        ];

        try {
            if (Schema::hasTable('student_video_exams')) {
                $videoExams = \DB::table('student_video_exams')
                    ->where('student_id', $student->id)
                    ->get();

                if ($videoExams->count() > 0) {
                    $videoExamStats['total_submissions'] = $videoExams->count();
                    $videoExamStats['validated'] = $videoExams->where('is_valid', 1)->count();
                    $videoExamStats['rejected'] = $videoExams->where('is_valid', -1)->count();
                    $videoExamStats['pending'] = $videoExams->where('is_valid', 0)->count();

                    $latestVideoExam = $videoExams->sortByDesc('submitted_at')->first();
                    if ($latestVideoExam) {
                        $videoExamStats['latest_submission'] = $latestVideoExam;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info('Video exam stats not available for student: ' . $e->getMessage());
        }

        // Customer Points
        $pointsService = new PointsService();
        $pointsBalance = $pointsService->getPointsBalance($student->id);
        $pointsHistory = $pointsService->getPointsHistory($student->id, 10);
        $pointsUsageOrders = \App\Models\Order::where('student_id', $student->id)
            ->where('points_used', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $blockedCourses = BlockStudentCourse::with(['course' => function ($query) {
                $query->with(['variations' => function ($q) {
                    $q->where('langue', 'ar');
                }]);
            }])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $blockedCourseIds = $blockedCourses->pluck('course_id')->flip();

        $enrolledCourseIds = BlockStudentCourse::getEnrolledCourseIds($student->id);
        $blockableCourseIds = $enrolledCourseIds->diff($blockedCourses->pluck('course_id'))->values();
        $blockableCourses = BlockStudentCourse::mapCoursesForBlock($blockableCourseIds);

        $emailLogs = EmailLog::forStudent($student->id, $student->email);

        return view('students.show', compact(
            'student',
            'enrolledCourses',
            'quizStats',
            'stageStats',
            'videoExamStats',
            'pointsBalance',
            'pointsHistory',
            'pointsUsageOrders',
            'blockedCourses',
            'blockedCourseIds',
            'blockableCourses',
            'emailLogs'
        ));
    }

    public function edit(Student $student)
    {
        $pointsBalance = (new PointsService())->getPointsBalance($student->id);

        return view('students.edit', compact('student', 'pointsBalance'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
            'country' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            'available_points' => 'required|integer|min:0|max:10000000',
        ]);

        $data = $request->except(['password', 'image', 'available_points']);

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($student->image) {
                $oldImagePath = public_path('uploads/students/' . $student->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/students'), $imageName);
            $data['image'] = $imageName;
        }

        $student->update($data);

        $pointsService = new PointsService();
        $currentAvailable = (int) $pointsService->getPointsBalance($student->id)['available_points'];
        $newAvailable = (int) $request->available_points;
        $delta = $newAvailable - $currentAvailable;

        if ($delta !== 0) {
            $pointsService->adjustPoints(
                $student->id,
                $delta,
                'Balance set from Edit Student'
            );
        }

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        // Delete image if exists
        if ($student->image) {
            $imagePath = public_path('uploads/students/' . $student->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }

    /**
     * Adjust customer points (admin action).
     */
    public function adjustPoints(Request $request, Student $student)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $pointsService = new PointsService();
        $pointsService->adjustPoints(
            $student->id,
            $request->points,
            $request->reason
        );

        $action = $request->points > 0 ? 'added' : 'deducted';
        $amount = abs($request->points);

        return redirect()->route('students.show', $student)
            ->with('success', "Successfully {$action} {$amount} points. Reason: {$request->reason}");
    }

    /**
     * Block a student account
     */
    public function block(Request $request, Student $student)
    {
        $request->validate([
            'block_reason' => 'required|string|max:500',
        ]);

        $student->update([
            'is_blocked' => true,
            'block_reason' => $request->block_reason,
            'blocked_at' => now(),
        ]);

        // Send email notification to the student
        try {
            Mail::to($student->email)->send(
                new AccountBlocked($student, $request->block_reason)
            );

            \App\Models\EmailLog::logSent(
                $student->email,
                'account_blocked',
                'Account Blocked Notification',
                $student->id,
                ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                'Student',
                $student->id
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send account blocked email', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            \App\Models\EmailLog::logFailed(
                $student->email,
                'account_blocked',
                'Account Blocked Notification',
                $e->getMessage(),
                $student->id,
                ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                'Student',
                $student->id
            );
        }

        return redirect()->route('students.show', $student)
            ->with('success', 'Student account has been blocked successfully.');
    }

    /**
     * Unblock a student account
     */
    public function unblock(Student $student)
    {
        $student->update([
            'is_blocked' => false,
            'block_reason' => null,
            'blocked_at' => null,
        ]);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student account has been unblocked successfully.');
    }

    /**
     * Search enrolled courses available to block for a student (AJAX).
     */
    public function searchCoursesForBlock(Request $request, Student $student)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $search = trim($request->get('q', ''));

        $enrolledCourseIds = BlockStudentCourse::getEnrolledCourseIds($student->id);

        $blockedCourseIds = BlockStudentCourse::where('student_id', $student->id)
            ->pluck('course_id');

        $availableIds = $enrolledCourseIds->diff($blockedCourseIds)->values();

        if ($availableIds->isEmpty()) {
            $reason = $enrolledCourseIds->isEmpty() ? 'no_enrollments' : 'all_blocked';

            return response()->json([
                'success' => true,
                'courses' => [],
                'reason' => $reason,
                'message' => $reason === 'all_blocked'
                    ? 'All enrolled courses are already blocked.'
                    : 'This student has no enrolled courses.',
            ]);
        }

        $courses = BlockStudentCourse::mapCoursesForBlock($availableIds);

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $courses = $courses->filter(function ($course) use ($needle) {
                return str_contains((string) $course['id'], $needle)
                    || str_contains(mb_strtolower($course['title']), $needle);
            })->values();
        }

        return response()->json([
            'success' => true,
            'courses' => $courses,
            'reason' => $courses->isEmpty() ? 'no_match' : null,
            'message' => $courses->isEmpty()
                ? 'No enrolled course matches your search.'
                : null,
        ]);
    }

    /**
     * Block or unblock a course for a student.
     */
    public function toggleBlockCourse(Student $student, $courseId)
    {
        try {
            Product::findOrFail($courseId);

            $enrolledCourseIds = BlockStudentCourse::getEnrolledCourseIds($student->id);

            if (!$enrolledCourseIds->contains((int) $courseId)) {
                throw new \RuntimeException('Student is not enrolled in this course.');
            }

            $course = Product::with('variations')->findOrFail($courseId);
            $courseTitle = BlockStudentCourse::getCourseTitle($course);

            $block = BlockStudentCourse::where('course_id', $courseId)
                ->where('student_id', $student->id)
                ->first();

            if ($block) {
                $block->delete();
                $message = "Successfully unblocked \"{$courseTitle}\" for this student.";
            } else {
                BlockStudentCourse::create([
                    'course_id' => $courseId,
                    'student_id' => $student->id,
                ]);
                $message = "Successfully blocked \"{$courseTitle}\" for this student.";
            }

            return redirect()->route('students.show', $student)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('students.show', $student)
                ->with('error', 'Error updating course block status. Please try again.');
        }
    }
}
