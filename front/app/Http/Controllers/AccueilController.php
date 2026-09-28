<?php

namespace App\Http\Controllers;

use App\Services\AccueilService;
use Illuminate\Http\Request;

class AccueilController extends Controller
{
    protected AccueilService $accueilService;

    public function __construct(AccueilService $accueilService)
    {
        $this->accueilService = $accueilService;
    }

    public function index(Request $request)
    {
        return $this->accueilService->handleIndex($request);
    }
}
