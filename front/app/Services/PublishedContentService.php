<?php

namespace App\Services;

use Illuminate\Http\Request;

class PublishedContentService
{
    public function paginate(Request $request, string $modelClass, int $perPage = 12)
    {
        $query = $modelClass::published()->orderBy('published_date', 'desc');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->search($search);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function showWithRecent(string $modelClass, string $slug, int $recentLimit = 5): array
    {
        $content = $modelClass::published()->where('slug', $slug)->firstOrFail();

        if (method_exists($content, 'incrementViews')) {
            $content->incrementViews();
        }

        $recentItems = $modelClass::published()
            ->whereKeyNot($content->getKey())
            ->orderBy('published_date', 'desc')
            ->limit($recentLimit)
            ->get();

        return [
            'content' => $content,
            'recentItems' => $recentItems,
        ];
    }
}
