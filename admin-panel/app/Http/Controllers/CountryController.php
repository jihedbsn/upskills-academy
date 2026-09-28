<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        return $this->countryService->index();
    }

    public function create()
    {
        return $this->countryService->create();
    }

    public function store(Request $request)
    {
        return $this->countryService->store($request);
    }

    public function show(Country $country)
    {
        return $this->countryService->show($country);
    }

    public function edit(Country $country)
    {
        return $this->countryService->edit($country);
    }

    public function update(Request $request, Country $country)
    {
        return $this->countryService->update($request, $country);
    }

    public function destroy(Country $country)
    {
        return $this->countryService->destroy($country);
    }
}
