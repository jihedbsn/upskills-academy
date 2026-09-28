<?php

namespace App\Http\Controllers;

use App\Models\Carousel;
use App\Services\CarouselService;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    protected CarouselService $carouselService;

    public function __construct(CarouselService $carouselService)
    {
        $this->carouselService = $carouselService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->carouselService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->carouselService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->carouselService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Carousel $carousel)
    {
        return $this->carouselService->show($carousel);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carousel $carousel)
    {
        return $this->carouselService->edit($carousel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carousel $carousel)
    {
        return $this->carouselService->update($request, $carousel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carousel $carousel)
    {
        return $this->carouselService->destroy($carousel);
    }
}
