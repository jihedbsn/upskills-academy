<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PageService
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        $pages = $query->ordered()->paginate(10);

        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        return view('pages.create');
    }

    public function store(Request $request)
    {
        Log::info('Page creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'titre_ar'       => 'required|string|max:255',
            'titre_en'       => 'required|string|max:255',
            'meta_title_ar'  => 'required|string|max:255',
            'meta_title_en'  => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'slug'           => 'nullable|string|max:255|unique:pages,slug',
            'is_active'      => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['titre_en']);
            }

            $slug = $data['slug'];
            $counter = 1;
            while (Page::where('slug', $slug)->exists()) {
                $slug = $data['slug'] . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;

            Log::info('Creating page with data', $data);
            $page = Page::create($data);
            Log::info('Page created successfully', ['id' => $page->id]);

            return redirect()->route('pages.index')->with('success', 'Page créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating page', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création de la page: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Page $page)
    {
        return view('pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'titre_ar'       => 'required|string|max:255',
            'titre_en'       => 'required|string|max:255',
            'meta_title_ar'  => 'required|string|max:255',
            'meta_title_en'  => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'slug'           => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'is_active'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['titre_en']);
        }

        $slug = $data['slug'];
        $counter = 1;
        while (Page::where('slug', $slug)->where('id', '!=', $page->id)->exists()) {
            $slug = $data['slug'] . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;

        $page->update($data);

        return redirect()->route('pages.index')->with('success', 'Page mise à jour avec succès.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('pages.index')->with('success', 'Page supprimée avec succès.');
    }

    public function updateOrder(Request $request)
    {
        try {
            Log::info('📤 Mise à jour de l\'ordre des pages - Données reçues:', [
                'all_data'     => $request->all(),
                'order_data'   => $request->input('order'),
                'headers'      => $request->headers->all(),
                'method'       => $request->method(),
                'content_type' => $request->header('Content-Type'),
            ]);

            $orderData = $request->input('order', []);

            if (is_string($orderData)) {
                $orderData = json_decode($orderData, true);
            }

            if (empty($orderData)) {
                Log::warning('❌ Aucune donnée d\'ordre fournie');
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée d\'ordre fournie',
                ], 400);
            }

            Log::info('📋 Données d\'ordre à traiter:', $orderData);

            $existingIds = Page::whereIn('id', $orderData)->pluck('id')->toArray();
            $missingIds  = array_diff($orderData, $existingIds);

            if (!empty($missingIds)) {
                Log::warning('❌ IDs de pages manquants:', $missingIds);
                return response()->json([
                    'success' => false,
                    'message' => 'Certaines pages n\'existent pas: ' . implode(', ', $missingIds),
                ], 400);
            }

            $updatedCount = 0;
            foreach ($orderData as $index => $pageId) {
                $newOrder = $index + 1;
                $result   = Page::where('id', $pageId)->update(['order' => $newOrder]);
                if ($result) {
                    $updatedCount++;
                    Log::info("✅ Page ID {$pageId} mise à jour avec l'ordre {$newOrder}");
                } else {
                    Log::warning("⚠️ Échec de mise à jour pour la page ID {$pageId}");
                }
            }

            Log::info("🎉 Mise à jour terminée: {$updatedCount} pages mises à jour sur " . count($orderData));

            return response()->json([
                'success' => true,
                'message' => "Ordre des pages mis à jour avec succès ({$updatedCount} pages mises à jour)",
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la mise à jour de l\'ordre des pages:', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'ordre: ' . $e->getMessage(),
            ], 500);
        }
    }
}
