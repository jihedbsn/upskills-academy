<?php

namespace App\Http\Controllers;

use App\Models\AboutAcceuil;
use App\Services\AboutAcceuilService;
use Illuminate\Http\Request;

class AboutAcceuilController extends Controller
{
    protected AboutAcceuilService $aboutAcceuilService;

    public function __construct(AboutAcceuilService $aboutAcceuilService)
    {
        $this->aboutAcceuilService = $aboutAcceuilService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->aboutAcceuilService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->aboutAcceuilService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->aboutAcceuilService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutAcceuil $aboutAcceuil)
    {
        return $this->aboutAcceuilService->show($aboutAcceuil);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AboutAcceuil $aboutAcceuil)
    {
        return $this->aboutAcceuilService->edit($aboutAcceuil);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AboutAcceuil $aboutAcceuil)
    {
        return $this->aboutAcceuilService->update($request, $aboutAcceuil);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutAcceuil $aboutAcceuil)
    {
        return $this->aboutAcceuilService->destroy($aboutAcceuil);
    }
}
