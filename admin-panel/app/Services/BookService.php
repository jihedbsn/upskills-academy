<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;

class BookService
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($queryBuilder) use ($search) {
                $queryBuilder->where('titre_ar', 'like', "%{$search}%")
                    ->orWhere('titre_en', 'like', "%{$search}%")
                    ->orWhere('description_short_ar', 'like', "%{$search}%")
                    ->orWhere('description_short_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        $books = $query->latest()->paginate(10);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre_ar' => 'required|string|max:255',
            'titre_en' => 'required|string|max:255',
            'description_short_ar' => 'nullable|string|max:500',
            'description_short_en' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,epub|max:512000',
            'summary' => 'nullable|file|mimes:pdf,doc,docx,epub|max:512000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'prix' => 'required|numeric|min:0|max:999999.99'
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            $data['file'] = $this->storeFile($request->file('file'), public_path('uploads/books'));
        }

        if ($request->hasFile('summary')) {
            $data['summary'] = $this->storeFile($request->file('summary'), public_path('uploads/books/summaries'));
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeFile($request->file('image'), public_path('uploads/books/images'));
        }

        Book::create($data);

        return redirect()->route('books.index')
            ->with('success', 'Livre créé avec succès !');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'titre_ar' => 'required|string|max:255',
            'titre_en' => 'required|string|max:255',
            'description_short_ar' => 'nullable|string|max:500',
            'description_short_en' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,epub|max:512000',
            'summary' => 'nullable|file|mimes:pdf,doc,docx,epub|max:512000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'prix' => 'required|numeric|min:0|max:999999.99'
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            $this->deleteIfExists($book->file, public_path('uploads/books'));
            $data['file'] = $this->storeFile($request->file('file'), public_path('uploads/books'));
        }

        if ($request->hasFile('summary')) {
            $this->deleteIfExists($book->summary, public_path('uploads/books/summaries'));
            $data['summary'] = $this->storeFile($request->file('summary'), public_path('uploads/books/summaries'));
        }

        if ($request->hasFile('image')) {
            $this->deleteIfExists($book->image, public_path('uploads/books/images'));
            $data['image'] = $this->storeFile($request->file('image'), public_path('uploads/books/images'));
        }

        $book->update($data);

        return redirect()->route('books.index')
            ->with('success', 'Livre mis à jour avec succès !');
    }

    public function destroy(Book $book)
    {
        $this->deleteIfExists($book->file, public_path('uploads/books'));
        $this->deleteIfExists($book->image, public_path('uploads/books/images'));
        $this->deleteIfExists($book->summary, public_path('uploads/books/summaries'));

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Livre supprimé avec succès !');
    }

    private function storeFile($file, string $directory): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $fileName);

        return $fileName;
    }

    private function deleteIfExists(?string $fileName, string $directory): void
    {
        if (!$fileName) {
            return;
        }

        $filePath = $directory . '/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
