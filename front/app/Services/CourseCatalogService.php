<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CourseSession;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\ZoomMeeting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CourseCatalogService
{
    public function index()
    {
        $courses = $this->listedCoursesQuery()->paginate(12);
        $categories = $this->getActiveCategories();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(string $locale, Product $course)
    {
        if (Auth::check()) {
            $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked($course->id, $locale);
            if ($blockResponse) {
                return $blockResponse;
            }
        }

        $course->load(['category', 'teacher', 'quizzes', 'videos', 'resources']);

        $similarCourses = Cache::remember(
            "courses:{$course->id}:similar",
            now()->addMinutes(10),
            fn () => $this->listedCoursesQuery()
                ->where('category_id', $course->category_id)
                ->where('id', '!=', $course->id)
                ->take(4)
                ->get()
        );

        $enrollment = null;
        if (Auth::check()) {
            $enrollment = ProductStudent::where('product_id', $course->id)
                ->where('student_id', Auth::id())
                ->first();
        }

        $courseSessions = Cache::remember(
            "courses:{$course->id}:sessions",
            now()->addMinutes(5),
            fn () => CourseSession::byCourse($course->id)
                ->orderBy('session_date')
                ->orderBy('start_time')
                ->get()
        );

        $upcomingSessions = $courseSessions->filter(function ($session) {
            return $session->isUpcoming() || $session->isToday();
        });

        $pastSessions = $courseSessions->filter(function ($session) {
            return $session->isPast() && !$session->isToday();
        });

        $zoomMeetings = Cache::remember(
            "courses:{$course->id}:zoom_meetings",
            now()->addMinutes(5),
            fn () => ZoomMeeting::where('product_id', $course->id)
                ->orderBy('start_time')
                ->get()
        );

        return view('courses.show', compact('course', 'similarCourses', 'enrollment', 'courseSessions', 'upcomingSessions', 'pastSessions', 'zoomMeetings'));
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $category = $request->get('category');

        $courses = $this->listedCoursesQuery();

        if ($query !== '') {
            $courses->where(function ($builder) use ($query) {
                $builder->where('titre', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if (!empty($category)) {
            $courses->where('category_id', $category);
        }

        $courses = $courses->paginate(12);
        $categories = $this->getActiveCategories();

        return view('courses.index', compact('courses', 'categories', 'query', 'category'));
    }

    private function listedCoursesQuery(): Builder
    {
        return Product::with(['category', 'teacher'])
            ->where('statut', 1)
            ->where('iscach', 1)
            ->where('is_listed', 1);
    }

    private function getActiveCategories()
    {
        return Cache::remember(
            'courses:active_categories',
            now()->addMinutes(15),
            fn () => Category::where('statut', 1)->get()
        );
    }
}
