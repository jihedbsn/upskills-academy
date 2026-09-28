<?php

namespace App\Http\Controllers;

use App\Models\Sujet;
use App\Services\SujetService;
use Illuminate\Http\Request;

class SujetController extends Controller
{
    public function __construct(private SujetService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('sujets.index', $data);
    }

    public function create()
    {
        return view('sujets.create');
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Sujet $sujet)
    {
        return view('sujets.show', compact('sujet'));
    }

    public function edit(Sujet $sujet)
    {
        return view('sujets.edit', compact('sujet'));
    }

    public function update(Request $request, Sujet $sujet)
    {
        return $this->service->update($request, $sujet);
    }

    public function destroy(Sujet $sujet)
    {
        return $this->service->destroy($sujet);
    }
}
