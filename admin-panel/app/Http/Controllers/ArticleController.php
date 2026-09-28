<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        return $this->articleService->index($request);
    }

    public function create()
    {
        return $this->articleService->create();
    }

    public function store(Request $request)
    {
        return $this->articleService->store($request);
    }

    public function show(Article $article)
    {
        return $this->articleService->show($article);
    }

    public function edit(Article $article)
    {
        return $this->articleService->edit($article);
    }

    public function update(Request $request, Article $article)
    {
        return $this->articleService->update($request, $article);
    }

    public function destroy(Article $article)
    {
        return $this->articleService->destroy($article);
    }
}
