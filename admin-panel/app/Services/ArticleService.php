<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleService
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $articles = $query->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        Log::info('Article creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'titre_ar' => 'required|string|max:255',
            'titre_en' => 'required|string|max:255',
            'meta_title_ar' => 'required|string|max:255',
            'meta_title_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_short_ar' => 'nullable|string|max:500',
            'description_short_en' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author_ar' => 'nullable|string|max:255',
            'author_en' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), $data['titre_en']);
            }

            $baseSlug = !empty($data['slug']) ? $data['slug'] : Str::slug($data['titre_en']);
            $data['slug'] = $this->generateUniqueSlug($baseSlug);

            Log::info('Creating article with data', $data);

            $article = Article::create($data);

            Log::info('Article created successfully', ['id' => $article->id]);

            return redirect()->route('articles.index')
                ->with('success', 'Article créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating article', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création de l\'article: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Article $article)
    {
        $article->incrementViews();

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'titre_ar' => 'required|string|max:255',
            'titre_en' => 'required|string|max:255',
            'meta_title_ar' => 'required|string|max:255',
            'meta_title_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_short_ar' => 'nullable|string|max:500',
            'description_short_en' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author_ar' => 'nullable|string|max:255',
            'author_en' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $this->deleteImageIfExists($article->image);
                $data['image'] = $this->uploadImage($request->file('image'), $data['titre_en']);
            }

            $baseSlug = !empty($data['slug']) ? $data['slug'] : Str::slug($data['titre_en']);
            $data['slug'] = $this->generateUniqueSlug($baseSlug, $article->id);

            $article->update($data);

            return redirect()->route('articles.index')
                ->with('success', 'Article mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Article $article)
    {
        try {
            $this->deleteImageIfExists($article->image);

            $article->delete();

            return redirect()->route('articles.index')
                ->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    private function uploadImage($image, string $title): string
    {
        $imageName = time() . '_' . Str::slug($title) . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('uploads/articles');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $image->move($uploadPath, $imageName);

        return $imageName;
    }

    private function deleteImageIfExists(?string $imageName): void
    {
        if ($imageName && file_exists(public_path('uploads/articles/' . $imageName))) {
            unlink(public_path('uploads/articles/' . $imageName));
        }
    }

    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Article::where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
