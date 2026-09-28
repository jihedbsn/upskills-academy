<?php

namespace App\Services;

use App\Models\Student;
use App\Models\EmailLog;
use App\Mail\AccountBlocked;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StudentService
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('country', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:students,email',
            'phone'      => 'nullable|string|max:50',
            'password'   => 'required|string|min:8',
            'country'    => 'nullable|string|max:100',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data             = $request->all();
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $image             = $request->file('image');
            $imageName         = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/students'), $imageName);
            $data['image']     = $imageName;
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully!');
    }

    public function show(Student $student): array
    {
        $student->load([
            'orders' => function ($query) {
                $query->where('payment_success', 1)->with('product.variations');
            },
        ]);

        $enrolledCourses = $student->orders()
            ->where('payment_success', 1)
            ->with('product.variations')
            ->orderBy('created_at', 'desc')
            ->get();

        $quizStats = [
            'total_attempts'    => 0,
            'successful_attempts' => 0,
            'failed_attempts'   => 0,
            'average_score'     => 0,
            'best_score'        => 0,
            'latest_quiz'       => null,
        ];

        try {
            if (Schema::hasTable('historique_quizzes')) {
                $quizHistory = DB::table('historique_quizzes')
                    ->where('student_id', $student->id)
                    ->get();

                if ($quizHistory->count() > 0) {
                    $quizStats['total_attempts']      = $quizHistory->count();
                    $quizStats['successful_attempts'] = $quizHistory->where('is_correct', true)->count();
                    $quizStats['failed_attempts']     = $quizHistory->where('is_correct', false)->count();
                    $quizStats['average_score']       = $quizHistory->avg('score') ?? 0;
                    $quizStats['best_score']          = $quizHistory->max('score') ?? 0;
                    $quizStats['latest_quiz']         = $quizHistory->sortByDesc('created_at')->first();
                }
            }
        } catch (\Exception $e) {
            Log::info('Quiz stats not available for student: ' . $e->getMessage());
        }

        $stageStats = [
            'total_submissions' => 0,
            'validated'         => 0,
            'rejected'          => 0,
            'pending'           => 0,
            'latest_submission' => null,
        ];

        try {
            if (Schema::hasTable('student_stage_courses')) {
                $stages = DB::table('student_stage_courses')
                    ->where('student_id', $student->id)
                    ->get();

                if ($stages->count() > 0) {
                    $stageStats['total_submissions'] = $stages->count();
                    $stageStats['validated']         = $stages->where('status', 'validated')->count();
                    $stageStats['rejected']          = $stages->where('status', 'rejected')->count();
                    $stageStats['pending']           = $stages->where('status', 'pending')->count();
                    $stageStats['latest_submission'] = $stages->sortByDesc('created_at')->first();
                }
            }
        } catch (\Exception $e) {
            Log::info('Stage stats not available for student: ' . $e->getMessage());
        }

        $videoExamStats = [
            'total_submissions' => 0,
            'validated'         => 0,
            'rejected'          => 0,
            'pending'           => 0,
            'latest_submission' => null,
        ];

        try {
            if (Schema::hasTable('student_video_exams')) {
                $videoExams = DB::table('student_video_exams')
                    ->where('student_id', $student->id)
                    ->get();

                if ($videoExams->count() > 0) {
                    $videoExamStats['total_submissions'] = $videoExams->count();
                    $videoExamStats['validated']         = $videoExams->where('status', 'validated')->count();
                    $videoExamStats['rejected']          = $videoExams->where('status', 'rejected')->count();
                    $videoExamStats['pending']           = $videoExams->where('status', 'pending')->count();
                    $videoExamStats['latest_submission'] = $videoExams->sortByDesc('created_at')->first();
                }
            }
        } catch (\Exception $e) {
            Log::info('Video exam stats not available for student: ' . $e->getMessage());
        }

        $pointsService  = new PointsService();
        $pointsBalance  = $pointsService->getPointsBalance($student->id);
        $pointsHistory  = $pointsService->getPointsHistory($student->id, 10);

        return compact(
            'student',
            'enrolledCourses',
            'quizStats',
            'stageStats',
            'videoExamStats',
            'pointsBalance',
            'pointsHistory'
        );
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:students,email,' . $student->id,
            'phone'      => 'nullable|string|max:50',
            'password'   => 'nullable|string|min:8',
            'country'    => 'nullable|string|max:100',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except(['password', 'image']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($student->image) {
                $oldImagePath = public_path('uploads/students/' . $student->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image         = $request->file('image');
            $imageName     = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/students'), $imageName);
            $data['image'] = $imageName;
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        if ($student->image) {
            $imagePath = public_path('uploads/students/' . $student->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }

    public function adjustPoints(Request $request, Student $student)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $points = (int) $request->points;
        $reason = $request->reason;

        DB::transaction(function () use ($student, $points, $reason) {
            $customerPoints = DB::table('customer_points')
                ->where('student_id', $student->id)
                ->first();

            if (!$customerPoints) {
                $newBalance = max(0, $points);
                DB::table('customer_points')->insert([
                    'student_id'       => $student->id,
                    'total_points'     => max(0, $points),
                    'available_points' => $newBalance,
                    'used_points'      => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            } else {
                $newBalance = max(0, $customerPoints->available_points + $points);
                DB::table('customer_points')
                    ->where('student_id', $student->id)
                    ->update([
                        'total_points'     => max(0, $customerPoints->total_points + ($points > 0 ? $points : 0)),
                        'available_points' => $newBalance,
                        'used_points'      => $points < 0
                            ? $customerPoints->used_points + abs($points)
                            : $customerPoints->used_points,
                        'updated_at'       => now(),
                    ]);
            }

            DB::table('points_transactions')->insert([
                'student_id'    => $student->id,
                'order_id'      => null,
                'type'          => $points >= 0 ? 'earn' : 'redeem',
                'points'        => abs($points),
                'amount'        => 0,
                'description'   => 'Admin adjustment: ' . $reason,
                'balance_after' => $newBalance,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        });

        $action = $points > 0 ? 'added' : 'deducted';
        $amount = abs($points);

        return redirect()->route('students.show', $student)
            ->with('success', "Successfully {$action} {$amount} points. Reason: {$reason}");
    }

    public function block(Request $request, Student $student)
    {
        $request->validate(['block_reason' => 'required|string|max:500']);

        $student->update([
            'is_blocked'   => true,
            'block_reason' => $request->block_reason,
            'blocked_at'   => now(),
        ]);

        try {
            Mail::to($student->email)->send(new AccountBlocked($student, $request->block_reason));

            EmailLog::logSent(
                $student->email,
                'account_blocked',
                'Account Blocked Notification',
                $student->id,
                ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                'Student',
                $student->id
            );
        } catch (\Exception $e) {
            Log::error('Failed to send account blocked email', [
                'student_id' => $student->id,
                'error'      => $e->getMessage(),
            ]);

            EmailLog::logFailed(
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

    public function unblock(Student $student)
    {
        $student->update([
            'is_blocked'   => false,
            'block_reason' => null,
            'blocked_at'   => null,
        ]);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student account has been unblocked successfully.');
    }
}
