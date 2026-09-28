<?php

namespace App\Http\Controllers;

use App\Models\AvisAcceuil;
use App\Services\AvisAcceuilService;
use Illuminate\Http\Request;

class AvisAcceuilController extends Controller
{
    protected AvisAcceuilService $avisAcceuilService;

    public function __construct(AvisAcceuilService $avisAcceuilService)
    {
        $this->avisAcceuilService = $avisAcceuilService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->avisAcceuilService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->avisAcceuilService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->avisAcceuilService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(AvisAcceuil $avisAcceuil)
    {
        return $this->avisAcceuilService->show($avisAcceuil);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AvisAcceuil $avisAcceuil)
    {
        return $this->avisAcceuilService->edit($avisAcceuil);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AvisAcceuil $avisAcceuil)
    {
        return $this->avisAcceuilService->update($request, $avisAcceuil);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvisAcceuil $avisAcceuil)
    {
        return $this->avisAcceuilService->destroy($avisAcceuil);
    }
}
