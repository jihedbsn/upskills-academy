<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->packageService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->packageService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->packageService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        return $this->packageService->show($package);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        return $this->packageService->edit($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        return $this->packageService->update($request, $package);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        return $this->packageService->destroy($package);
    }

    /**
     * Toggle the active status of a package
     */
    public function toggle(Package $package)
    {
        return $this->packageService->toggle($package);
    }

    /**
     * Get package statistics
     */
    public function statistics()
    {
        return $this->packageService->statistics();
    }
}
