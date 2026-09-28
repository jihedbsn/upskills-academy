<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quiz;
use App\Services\ProductQuizService;
use Illuminate\Http\Request;

class ProductQuizController extends Controller
{
    public function __construct(protected ProductQuizService $service) {}

    public function index(Product $product)
    {
        $data = $this->service->index($product);
        return view('products.quizzes.index', $data);
    }

    public function store(Request $request, Product $product)
    {
        $this->service->store($request, $product);
        return redirect()->back()->with('success', 'Quiz ajoutés avec succès au produit.');
    }

    public function destroy(Product $product, Quiz $quiz)
    {
        return $this->service->destroy($product, $quiz);
    }

    public function updateInstallmentMonth(Request $request, Product $product, Quiz $quiz)
    {
        $this->service->updateInstallmentMonth($request, $product, $quiz);
        return redirect()->back()->with('success', 'Installment month updated successfully.');
    }

    public function search(Request $request)
    {
        return response()->json($this->service->search($request));
    }
}
