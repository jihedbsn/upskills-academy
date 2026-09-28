<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CourseCatalogService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseCatalogService $courseCatalogService;

    public function __construct(CourseCatalogService $courseCatalogService)
    {
        $this->courseCatalogService = $courseCatalogService;
    }

    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        return $this->courseCatalogService->index();
    }

    /**
     * Display the specified course.
     */
    public function show($locale, Product $course)
    {
        return $this->courseCatalogService->show((string) $locale, $course);
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        return $this->courseCatalogService->search($request);
    }
}
