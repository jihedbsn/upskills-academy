<?php

namespace App\Http\Controllers;

use App\Models\AccueilChiffre;
use App\Services\AccueilChiffreService;
use Illuminate\Http\Request;

class AccueilChiffreController extends Controller
{
    protected AccueilChiffreService $accueilChiffreService;

    public function __construct(AccueilChiffreService $accueilChiffreService)
    {
        $this->accueilChiffreService = $accueilChiffreService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->accueilChiffreService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->accueilChiffreService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->accueilChiffreService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(AccueilChiffre $accueilChiffre)
    {
        return $this->accueilChiffreService->show($accueilChiffre);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccueilChiffre $accueilChiffre)
    {
        return $this->accueilChiffreService->edit($accueilChiffre);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccueilChiffre $accueilChiffre)
    {
        return $this->accueilChiffreService->update($request, $accueilChiffre);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccueilChiffre $accueilChiffre)
    {
        return $this->accueilChiffreService->destroy($accueilChiffre);
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggle(AccueilChiffre $accueilChiffre)
    {
        return $this->accueilChiffreService->toggle($accueilChiffre);
    }
}
