<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\PublishedContentService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected PublishedContentService $publishedContentService;

    public function __construct(PublishedContentService $publishedContentService)
    {
        $this->publishedContentService = $publishedContentService;
    }

    /**
     * Afficher la liste des articles
     */
    public function index(Request $request)
    {
        $articles = $this->publishedContentService->paginate($request, Article::class, 12);

        return view('articles.index', compact('articles'));
    }

    /**
     * Afficher le détail d'un article
     */
    public function show($locale, $slug)
    {
        $contentData = $this->publishedContentService->showWithRecent(Article::class, $slug, 5);
        $article = $contentData['content'];
        $recentArticles = $contentData['recentItems'];

        return view('articles.show', compact('article', 'recentArticles'));
    }
}
