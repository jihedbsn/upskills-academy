<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NosPartenaires;
use App\Services\NosPartenairesService;

class NosPartenairesController extends Controller
{
    protected NosPartenairesService $nosPartenairesService;

    public function __construct(NosPartenairesService $nosPartenairesService)
    {
        $this->nosPartenairesService = $nosPartenairesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->nosPartenairesService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->nosPartenairesService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->nosPartenairesService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(NosPartenaires $nosPartenaires)
    {
        return $this->nosPartenairesService->show($nosPartenaires);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NosPartenaires $nosPartenaires)
    {
        return $this->nosPartenairesService->edit($nosPartenaires);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NosPartenaires $nosPartenaires)
    {
        return $this->nosPartenairesService->update($request, $nosPartenaires);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NosPartenaires $nosPartenaires)
    {
        return $this->nosPartenairesService->destroy($nosPartenaires);
    }

    /**
     * Mettre à jour l'ordre des partenaires
     */
    public function updateOrder(Request $request)
    {
        return $this->nosPartenairesService->updateOrder($request);
    }
}
