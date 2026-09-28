<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\BookCatalogService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookCatalogService $bookCatalogService;

    public function __construct(BookCatalogService $bookCatalogService)
    {
        $this->bookCatalogService = $bookCatalogService;
    }

    public function index(Request $request)
    {
        $data = $this->bookCatalogService->getIndexData($request);

        return view('books.index', $data);
    }

    public function show($locale, Book $book)
    {
        return view('books.show', $this->bookCatalogService->getShowData($book));
    }
}
