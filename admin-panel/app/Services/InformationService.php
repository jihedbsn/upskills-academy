<?php

namespace App\Services;

use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformationService
{
    public function index()
    {
        $information = Information::firstOrCreate([]);

        return view('information.index', compact('information'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $information = Information::firstOrCreate([]);
        $information->update($request->all());

        return redirect()->route('information.index')
            ->with('success', 'Site information updated successfully!');
    }
}
