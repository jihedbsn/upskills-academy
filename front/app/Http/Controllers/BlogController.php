<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Services\PublishedContentService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected PublishedContentService $publishedContentService;

    public function __construct(PublishedContentService $publishedContentService)
    {
        $this->publishedContentService = $publishedContentService;
    }

    /**
     * Afficher la liste des blogs
     */
    public function index(Request $request)
    {
        $blogs = $this->publishedContentService->paginate($request, Blog::class, 12);

        return view('blog.index', compact('blogs'));
    }

    /**
     * Afficher le détail d'un blog
     */
    public function show($locale, $slug)
    {
        $contentData = $this->publishedContentService->showWithRecent(Blog::class, $slug, 5);
        $blog = $contentData['content'];
        $recentBlogs = $contentData['recentItems'];

        return view('blog.show', compact('blog', 'recentBlogs'));
    }
}
