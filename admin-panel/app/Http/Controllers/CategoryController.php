<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return $this->categoryService->index();
    }

    public function create()
    {
        return $this->categoryService->create();
    }

    public function store(Request $request)
    {
        return $this->categoryService->store($request);
    }

    public function show(Category $category)
    {
        return $this->categoryService->show($category);
    }

    public function edit(Category $category)
    {
        return $this->categoryService->edit($category);
    }

    public function update(Request $request, Category $category)
    {
        return $this->categoryService->update($request, $category);
    }

    public function destroy(Category $category)
    {
        return $this->categoryService->destroy($category);
    }

    public function updateOrder(Request $request)
    {
        return $this->categoryService->updateOrder($request);
    }
}
