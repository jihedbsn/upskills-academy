<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Country;
use App\Models\CourseRating;
use App\Models\Discussion;
use App\Models\Order;
use App\Models\Product;
use App\Models\Resource;
use App\Models\ResultatQuiz;
use App\Models\Student;
use App\Models\StudentStageCourse;
use App\Models\StudentSuccess;
use App\Models\StudentVideoExam;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardService
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        $data = [
            'productsCount' => Product::count(),
            'categoriesCount' => Category::count(),
            'teachersCount' => Teacher::count(),
            'countriesCount' => Country::count(),
            'resourcesCount' => Resource::count(),

            'ordersCount' => Order::whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'successfulOrdersCount' => Order::where('payment_success', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'pendingOrdersCount' => Order::where('payment_success', false)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'totalRevenue' => Order::where('payment_success', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->sum('price'),

            'quizResultsCount' => ResultatQuiz::whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'successfulQuizResultsCount' => ResultatQuiz::where('success', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'failedQuizResultsCount' => ResultatQuiz::where('success', false)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'averageQuizScore' => ResultatQuiz::where('success', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->avg('score') ?? 0,

            'discussionsCount' => Discussion::whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'approvedDiscussionsCount' => Discussion::where('is_approved', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'pendingDiscussionsCount' => Discussion::where('is_approved', false)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),

            'ratingsCount' => CourseRating::whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'approvedRatingsCount' => CourseRating::where('is_approved', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'pendingRatingsCount' => CourseRating::where('is_approved', false)->whereBetween('created_at', [$startCarbon, $endCarbon])->count(),
            'averageRating' => CourseRating::where('is_approved', true)->whereBetween('created_at', [$startCarbon, $endCarbon])->avg('rating') ?? 0,

            'studentsCount' => Student::count(),
            'booksCount' => Book::count(),

            'pendingStageSubmissionsCount' => StudentStageCourse::where('is_valid', 0)->count(),
            'pendingVideoExamsCount' => StudentVideoExam::where('is_valid', 0)->count(),
            'pendingStudentSuccessesCount' => StudentSuccess::where('success', 0)->count(),
            'pendingStageSubmissions' => StudentStageCourse::with(['student', 'product.variations'])
                ->where('is_valid', 0)
                ->whereHas('student')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'pendingVideoExams' => StudentVideoExam::with(['student', 'product.variations'])
                ->where('is_valid', 0)
                ->whereHas('student')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'pendingStudentSuccesses' => StudentSuccess::with(['student', 'product.variations'])
                ->where('success', 0)
                ->whereHas('student')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        $ordersByMonth = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereBetween('created_at', [$startCarbon, $endCarbon])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $ordersChartData = [];
        $ordersChartLabels = [];

        $currentDate = $startCarbon->copy();
        while ($currentDate->lte($endCarbon)) {
            $monthName = $currentDate->format('M Y');
            $ordersChartLabels[] = $monthName;

            $orderCount = $ordersByMonth->where('month', $currentDate->month)->where('year', $currentDate->year)->first();
            $ordersChartData[] = $orderCount ? $orderCount->count : 0;

            $currentDate->addMonth();
        }

        $quizResultsByMonth = ResultatQuiz::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereBetween('created_at', [$startCarbon, $endCarbon])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $quizChartData = [];
        $currentDate = $startCarbon->copy();
        while ($currentDate->lte($endCarbon)) {
            $quizCount = $quizResultsByMonth->where('month', $currentDate->month)->where('year', $currentDate->year)->first();
            $quizChartData[] = $quizCount ? $quizCount->count : 0;
            $currentDate->addMonth();
        }

        $discussionsByMonth = Discussion::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereBetween('created_at', [$startCarbon, $endCarbon])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $discussionsChartData = [];
        $currentDate = $startCarbon->copy();
        while ($currentDate->lte($endCarbon)) {
            $discussionCount = $discussionsByMonth->where('month', $currentDate->month)->where('year', $currentDate->year)->first();
            $discussionsChartData[] = $discussionCount ? $discussionCount->count : 0;
            $currentDate->addMonth();
        }

        $ratingsByMonth = CourseRating::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereBetween('created_at', [$startCarbon, $endCarbon])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $ratingsChartData = [];
        $currentDate = $startCarbon->copy();
        while ($currentDate->lte($endCarbon)) {
            $ratingCount = $ratingsByMonth->where('month', $currentDate->month)->where('year', $currentDate->year)->first();
            $ratingsChartData[] = $ratingCount ? $ratingCount->count : 0;
            $currentDate->addMonth();
        }

        $ordersByStatus = [
            'Réussies' => $data['successfulOrdersCount'],
            'En attente' => $data['pendingOrdersCount']
        ];

        $quizByStatus = [
            'Réussis' => $data['successfulQuizResultsCount'],
            'Échoués' => $data['failedQuizResultsCount']
        ];

        $discussionsByStatus = [
            'Approuvées' => $data['approvedDiscussionsCount'],
            'En attente' => $data['pendingDiscussionsCount']
        ];

        $ratingsByStatus = [
            'Approuvées' => $data['approvedRatingsCount'],
            'En attente' => $data['pendingRatingsCount']
        ];

        return view('dashboard', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'ordersChartLabels' => $ordersChartLabels,
            'ordersChartData' => $ordersChartData,
            'quizChartData' => $quizChartData,
            'discussionsChartData' => $discussionsChartData,
            'ratingsChartData' => $ratingsChartData,
            'ordersByStatus' => $ordersByStatus,
            'quizByStatus' => $quizByStatus,
            'discussionsByStatus' => $discussionsByStatus,
            'ratingsByStatus' => $ratingsByStatus,
        ]));
    }
}
