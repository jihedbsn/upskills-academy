<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CourseSession;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\ZoomMeeting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FilterCatalogService
{
    public function index(Request $request)
    {
        $showType = $request->get('show_type', 'all');

        $query = $this->listedCoursesBaseQuery()
            ->with(['category', 'variations', 'studies', 'teacher']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('variations', function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            switch ($request->type) {
                case 'internet':
                    $query->where('online', 1);
                    break;
                case 'zoom':
                    $query->where('online', 0)->where('classroom', 0);
                    break;
                case 'classroom':
                    $query->where('classroom', 1);
                    break;
            }
        }

        if ($request->filled('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('prix', 0);
            } elseif ($request->price_type === 'paid') {
                $query->where('prix', '>', 0);
            }
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        if ($request->filled('category')) {
            $query->where('categories_id', $request->category);
        }

        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('prix', 'asc');
                break;
            case 'price_high':
                $query->orderBy('prix', 'desc');
                break;
            case 'title_az':
                $query->join('product_variations', 'products.id', '=', 'product_variations.products_id')
                    ->where('product_variations.langue', app()->getLocale())
                    ->orderBy('product_variations.name', 'asc')
                    ->select('products.*');
                break;
            case 'title_za':
                $query->join('product_variations', 'products.id', '=', 'product_variations.products_id')
                    ->where('product_variations.langue', app()->getLocale())
                    ->orderBy('product_variations.name', 'desc')
                    ->select('products.*');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $courses = $query->paginate(12);

        $packages = new LengthAwarePaginator([], 0, 12, 1);

        $baseStatsQuery = $this->listedCoursesBaseQuery();
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'free' => (clone $baseStatsQuery)->where('prix', 0)->count(),
            'paid' => (clone $baseStatsQuery)->where('prix', '>', 0)->count(),
            'packages' => Package::where('is_active', 1)->count(),
        ];

        $types = ['internet', 'zoom', 'classroom'];
        $categories = Category::withCount(['products' => function ($builder) {
            $builder->where('statut', 1)->where('iscach', 1)->where('is_listed', 1);
        }])->get();
        $maxPrice = (clone $baseStatsQuery)->max('prix') ?? 1000;
        $priceRanges = [
            ['min' => 0, 'max' => 50, 'label' => '$0 - $50'],
            ['min' => 50, 'max' => 100, 'label' => '$50 - $100'],
            ['min' => 100, 'max' => 200, 'label' => '$100 - $200'],
            ['min' => 200, 'max' => null, 'label' => '$200+'],
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('filter.components.contenu', compact('courses', 'packages', 'showType'))->render(),
                'sidebar' => view('filter.components.left-menu', compact('categories', 'types', 'stats', 'priceRanges', 'maxPrice'))->render(),
            ]);
        }

        return view('filter.filter', compact('courses', 'packages', 'showType', 'types', 'categories', 'priceRanges', 'stats', 'maxPrice'));
    }

    public function show(string $locale, $product)
    {
        $studentId = Auth::id();
        $isEnrolled = false;

        if ($studentId) {
            $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked((int) $product, $locale);
            if ($blockResponse) {
                return $blockResponse;
            }

            $isEnrolled = ProductStudent::where('product_id', $product)
                ->where('student_id', $studentId)
                ->where('is_active', true)
                ->exists();
        }

        $productQuery = Product::where('id', $product)
            ->where('statut', 1);

        if (!$isEnrolled) {
            $productQuery->where('iscach', 1);
        }

        $product = $productQuery->firstOrFail();

        $product->load([
            'variations',
            'studies.resource',
            'teacher',
            'category',
            'quizzes.questions.reponses',
            'quizzes.type',
        ]);

        $discussions = $product->getApprovedDiscussions()->get();

        $relatedCourses = Product::where('id', '!=', $product->id)
            ->where('categories_id', $product->categories_id)
            ->where('statut', 1)
            ->where('iscach', 1)
            ->where('is_listed', 1)
            ->with(['variations', 'studies', 'teacher'])
            ->limit(4)
            ->get();

        $enrollment = null;
        if (Auth::check()) {
            $enrollment = ProductStudent::where('product_id', $product->id)
                ->where('student_id', Auth::id())
                ->first();
        }

        $courseSessions = CourseSession::where('product_id', $product->id)
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $upcomingSessions = $courseSessions->filter(function ($session) {
            /** @var CourseSession $session */
            return $session->isUpcoming() || $session->isToday();
        });

        $pastSessions = $courseSessions->filter(function ($session) {
            /** @var CourseSession $session */
            return $session->isPast() && !$session->isToday();
        });

        $zoomMeetings = ZoomMeeting::where('product_id', $product->id)
            ->orderBy('start_time')
            ->get();

        $installmentContentInfo = null;
        if (Auth::check()) {
            $installmentService = app(InstallmentService::class);
            $installmentContentInfo = $installmentService->getUnlockedContent($product->id, Auth::id());
        }

        return view('courses.show', compact('product', 'relatedCourses', 'discussions', 'enrollment', 'courseSessions', 'upcomingSessions', 'pastSessions', 'zoomMeetings', 'installmentContentInfo'));
    }

    private function listedCoursesBaseQuery()
    {
        return Product::where('statut', 1)
            ->where('iscach', 1)
            ->where('is_listed', 1);
    }
}
