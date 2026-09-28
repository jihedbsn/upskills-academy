<?php

namespace App\Services;

use App\Jobs\SendStudentEnrollmentEmail;
use App\Models\BlockStudentCourse;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseStudentService
{
    public function index()
    {
        try {
            $courses = Product::with(['variations' => function ($query) {
                $query->where('langue', 'ar');
            }])
                ->withCount(['orders as students_count' => function ($query) {
                    $query->where('payment_success', 1)
                        ->whereHas('student');
                }])
                ->having('students_count', '>', 0)
                ->orderBy('students_count', 'desc')
                ->paginate(25);

            $courses->each(function ($course) {
                if ($course->variations->isEmpty()) {
                    $course->setAttribute('titre', 'Cours #' . $course->id);
                }
            });

            return view('course-students.index', compact('courses'));
        } catch (\Exception $e) {
            Log::error('Erreur dans CourseStudentController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $courses = collect();
            return view('course-students.index', compact('courses'));
        }
    }

    public function show($id)
    {
        try {
            $course = Product::with(['variations' => function ($query) {
                $query->where('langue', 'ar');
            }])->findOrFail($id);

            if ($course->variations->isEmpty()) {
                $course->setAttribute('titre', 'Cours #' . $course->id);
            }

            $students = Order::with('student')
                ->where('product_id', $id)
                ->where('payment_success', 1)
                ->whereHas('student')
                ->orderBy('created_at', 'desc')
                ->get();

            $blockedStudentIds = BlockStudentCourse::where('course_id', $id)
                ->pluck('student_id')
                ->flip();

            return view('course-students.show', compact('course', 'students', 'blockedStudentIds'));
        } catch (\Exception $e) {
            Log::error('Erreur dans CourseStudentController@show: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('course-students.index')
                ->with('error', 'Erreur lors du chargement des détails du cours');
        }
    }

    public function removeEnrollment($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $courseId = $order->product_id;
            $studentId = $order->student_id;
            $studentName = $order->student ? $order->student->first_name . ' ' . $order->student->last_name : 'Student';

            DB::table('product_students')
                ->where('product_id', $courseId)
                ->where('student_id', $studentId)
                ->delete();

            $order->delete();

            Log::info("Student enrollment removed: Order ID {$orderId}, Student ID {$studentId}, Course ID {$courseId}");

            return redirect()->route('course-students.show', $courseId)
                ->with('success', "Successfully removed {$studentName}'s enrollment from the course.");
        } catch (\Exception $e) {
            Log::error('Error removing enrollment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error removing student enrollment. Please try again.');
        }
    }

    public function addStudent(Request $request, $courseId)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            $studentId = $request->student_id;
            $course = Product::findOrFail($courseId);
            $student = Student::findOrFail($studentId);

            DB::beginTransaction();

            $existingOrder = Order::where('student_id', $studentId)
                ->where('product_id', $courseId)
                ->where('payment_success', 1)
                ->first();

            if ($existingOrder) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'Student is already enrolled in this course.');
            }

            Order::create([
                'student_id' => $studentId,
                'product_id' => $courseId,
                'payment_success' => 1,
                'payment_method' => 'manual',
                'price' => $course->prix ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existingProductStudent = ProductStudent::where('student_id', $studentId)
                ->where('product_id', $courseId)
                ->first();

            if (!$existingProductStudent) {
                ProductStudent::create([
                    'student_id' => $studentId,
                    'product_id' => $courseId,
                    'date' => now()->toDateString(),
                    'is_active' => true,
                    'access_granted_at' => now(),
                ]);
            } else {
                if (!$existingProductStudent->is_active) {
                    $existingProductStudent->grantAccess();
                }
            }

            try {
                SendStudentEnrollmentEmail::dispatch($student, $course, 'course_enrollment');
                Log::info("Enrollment email queued for student {$studentId} in course {$courseId}");
            } catch (\Exception $e) {
                Log::error('Failed to queue enrollment email: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Student successfully enrolled in the course.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding student to course: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error enrolling student. Please try again.');
        }
    }

    public function getAvailableStudents($courseId)
    {
        try {
            $enrolledStudentIds = Order::where('product_id', $courseId)
                ->where('payment_success', 1)
                ->whereHas('student')
                ->whereNotNull('student_id')
                ->pluck('student_id');

            $students = Student::whereNotIn('id', $enrolledStudentIds)
                ->whereNotNull('email')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'email' => $student->email,
                    ];
                });

            return response()->json([
                'success' => true,
                'students' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading students'
            ], 500);
        }
    }

    public function toggleBlockStudent($courseId, $studentId)
    {
        try {
            Product::findOrFail($courseId);

            $order = Order::where('product_id', $courseId)
                ->where('student_id', $studentId)
                ->where('payment_success', 1)
                ->whereHas('student')
                ->with('student')
                ->firstOrFail();

            $studentName = $order->student->first_name . ' ' . $order->student->last_name;

            $block = BlockStudentCourse::where('course_id', $courseId)
                ->where('student_id', $studentId)
                ->first();

            if ($block) {
                $block->delete();
                $message = "Successfully unblocked {$studentName} for this course.";
            } else {
                BlockStudentCourse::create([
                    'course_id' => $courseId,
                    'student_id' => $studentId,
                ]);
                $message = "Successfully blocked {$studentName} from this course.";
            }

            return redirect()->route('course-students.show', $courseId)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling student block: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error updating student block status. Please try again.');
        }
    }
}
