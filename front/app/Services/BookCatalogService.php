<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookCatalogService
{
    private const BOOK_STATS_CACHE_KEY = 'books.index.stats';
    private const BOOK_MAX_PRICE_CACHE_KEY = 'books.index.max_price';
    private const BOOK_STATS_CACHE_TTL_MINUTES = 10;

    public function getIndexData(Request $request): array
    {
        $query = Book::query();

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('titre_ar', 'like', "%{$search}%")
                    ->orWhere('titre_en', 'like', "%{$search}%")
                    ->orWhere('description_short_ar', 'like', "%{$search}%")
                    ->orWhere('description_short_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', (float) $request->query('prix_min'));
        }

        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', (float) $request->query('prix_max'));
        }

        $priceType = (string) $request->query('price_type', '');
        if ($priceType === 'free') {
            $query->where('prix', 0);
        } elseif ($priceType === 'paid') {
            $query->where('prix', '>', 0);
        }

        $this->applySort($query, (string) $request->query('sort', 'latest'));

        return [
            'books' => $query->paginate(12)->withQueryString(),
            'priceRanges' => $this->getPriceRanges(),
            'stats' => $this->getStats(),
            'maxPrice' => $this->getMaxPrice(),
        ];
    }

    public function getShowData(Book $book): array
    {
        $relatedBooks = Book::query()
            ->whereKeyNot($book->id)
            ->latest()
            ->limit(4)
            ->get();

        return [
            'book' => $book,
            'relatedBooks' => $relatedBooks,
        ];
    }

    private function applySort($query, string $sort): void
    {
        switch ($sort) {
            case 'price_low':
                $query->orderBy('prix', 'asc');
                return;

            case 'price_high':
                $query->orderBy('prix', 'desc');
                return;

            case 'title_az':
                $query->orderBy($this->getLocalizedTitleColumn(), 'asc');
                return;

            case 'title_za':
                $query->orderBy($this->getLocalizedTitleColumn(), 'desc');
                return;

            default:
                $query->latest();
                return;
        }
    }

    private function getLocalizedTitleColumn(): string
    {
        return app()->getLocale() === 'en' ? 'titre_en' : 'titre_ar';
    }

    private function getPriceRanges(): array
    {
        return [
            ['min' => 0, 'max' => 10, 'label' => '$0 - $10'],
            ['min' => 10, 'max' => 25, 'label' => '$10 - $25'],
            ['min' => 25, 'max' => 50, 'label' => '$25 - $50'],
            ['min' => 50, 'max' => 100, 'label' => '$50 - $100'],
            ['min' => 100, 'max' => null, 'label' => '$100+'],
        ];
    }

    private function getStats(): array
    {
        return Cache::remember(
            self::BOOK_STATS_CACHE_KEY,
            now()->addMinutes(self::BOOK_STATS_CACHE_TTL_MINUTES),
            function () {
                return [
                    'total' => Book::count(),
                    'free' => Book::where('prix', 0)->count(),
                    'paid' => Book::where('prix', '>', 0)->count(),
                ];
            }
        );
    }

    private function getMaxPrice(): float
    {
        return (float) Cache::remember(
            self::BOOK_MAX_PRICE_CACHE_KEY,
            now()->addMinutes(self::BOOK_STATS_CACHE_TTL_MINUTES),
            function () {
                return Book::max('prix') ?? 1000;
            }
        );
    }
}
