<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;

class HomeService
{
    public function index()
    {
        $popularCourses = Product::with(['category', 'teacher'])
            ->where('statut', 1)
            ->where('iscach', 1)
            ->where('is_listed', 1)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $categories = Category::where('statut', 1)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $recentArticles = Article::where('statut', 1)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('popularCourses', 'categories', 'recentArticles'));
    }
}
