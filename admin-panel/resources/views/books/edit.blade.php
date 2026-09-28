@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Edit Book</h1>
        <div class="page-actions">
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Edit Book</h4>
                            <p class="text-muted mb-0">Update the book information</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Validation errors</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Arabic section -->
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="align-right" class="me-2"></i>
                                            <h6 class="mb-0">Arabic Section</h6>
                                            <span class="badge bg-light text-dark ms-auto">العربية</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Title (Arabic) *
                                            </label>
                                            <input type="text" name="titre_ar" class="form-control @error('titre_ar') is-invalid @enderror" 
                                                   value="{{ old('titre_ar', $book->titre_ar) }}" required placeholder="Book title in Arabic">
                                            @error('titre_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Short Description (Arabic)
                                            </label>
                                            <textarea name="description_short_ar" class="form-control @error('description_short_ar') is-invalid @enderror" 
                                                      rows="3" placeholder="Short description in Arabic">{{ old('description_short_ar', $book->description_short_ar) }}</textarea>
                                            @error('description_short_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="edit-3" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Full Description (Arabic)
                                            </label>
                                            <textarea name="description_ar" id="description_ar" class="form-control @error('description_ar') is-invalid @enderror" 
                                                      rows="6" placeholder="Full description in Arabic">{{ old('description_ar', $book->description_ar) }}</textarea>
                                            @error('description_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- English section -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="align-left" class="me-2"></i>
                                            <h6 class="mb-0">English Section</h6>
                                            <span class="badge bg-light text-dark ms-auto">English</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Title (English) *
                                            </label>
                                            <input type="text" name="titre_en" class="form-control @error('titre_en') is-invalid @enderror" 
                                                   value="{{ old('titre_en', $book->titre_en) }}" required placeholder="Book title in English">
                                            @error('titre_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Short Description (English)
                                            </label>
                                            <textarea name="description_short_en" class="form-control @error('description_short_en') is-invalid @enderror" 
                                                      rows="3" placeholder="Short description in English">{{ old('description_short_en', $book->description_short_en) }}</textarea>
                                            @error('description_short_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="edit-3" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Full Description (English)
                                            </label>
                                            <textarea name="description_en" id="description_en" class="form-control @error('description_en') is-invalid @enderror" 
                                                      rows="6" placeholder="Full description in English">{{ old('description_en', $book->description_en) }}</textarea>
                                            @error('description_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File and Price section -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="settings" class="me-2"></i>
                                            <h6 class="mb-0">Configuration</h6>
                                        </div>
                                    </div>
                                                                         <div class="card-body">
                                         <div class="row">
                                             <div class="col-md-3">
                                                 <div class="mb-3">
                                                     <label class="form-label">
                                                         <i data-feather="image" class="me-1" style="width: 14px; height: 14px;"></i>
                                                         Book Image
                                                     </label>
                                                     @if($book->image)
                                                         <div class="mb-2">
                                                             <strong>Current image:</strong>
                                                             <div class="mt-2">
                                                                 <img src="{{ $book->image_url }}" alt="{{ $book->titre_ar }}" 
                                                                      class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                                             </div>
                                                         </div>
                                                     @endif
                                                     <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                                            accept="image/*">
                                                     <div class="form-text">Accepted formats: JPG, PNG, GIF, WEBP (max 2MB). Leave empty to keep current image.</div>
                                                     @error('image')
                                                         <div class="invalid-feedback">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                             </div>
                                             <div class="col-md-3">
                                                 <div class="mb-3">
                                                     <label class="form-label">
                                                         <i data-feather="file" class="me-1" style="width: 14px; height: 14px;"></i>
                                                         Book File
                                                     </label>
                                                     @if($book->file)
                                                         <div class="mb-2">
                                                             <strong>Current file:</strong>
                                                             <a href="{{ $book->file_url }}" target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                                                 <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                                                 {{ $book->file }}
                                                             </a>
                                                         </div>
                                                     @endif
                                                     <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" 
                                                            accept=".pdf,.doc,.docx,.epub">
                                                     <div class="form-text">Accepted formats: PDF, DOC, DOCX, EPUB (max 10MB). Leave empty to keep current file.</div>
                                                     @error('file')
                                                         <div class="invalid-feedback">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                             </div>
                                             <div class="col-md-3">
                                                 <div class="mb-3">
                                                     <label class="form-label">
                                                         <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                                         Book Summary
                                                     </label>
                                                     @if($book->summary)
                                                         <div class="mb-2">
                                                             <strong>Current summary:</strong>
                                                             <a href="{{ $book->summary_url }}" target="_blank" class="btn btn-sm btn-outline-warning ms-2">
                                                                 <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                                                                 {{ $book->summary }}
                                                             </a>
                                                         </div>
                                                     @endif
                                                     <input type="file" name="summary" class="form-control @error('summary') is-invalid @enderror" 
                                                            accept=".pdf,.doc,.docx,.epub">
                                                     <div class="form-text">Accepted formats: PDF, DOC, DOCX, EPUB (max 10MB). Leave empty to keep current summary.</div>
                                                     @error('summary')
                                                         <div class="invalid-feedback">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                             </div>
                                             <div class="col-md-3">
                                                 <div class="mb-3">
                                                     <label class="form-label">
                                                         <i data-feather="dollar-sign" class="me-1" style="width: 14px; height: 14px;"></i>
                                                         Price *
                                                     </label>
                                                     <div class="input-group">
                                                         <input type="number" name="prix" class="form-control @error('prix') is-invalid @enderror" 
                                                                value="{{ old('prix', $book->prix) }}" required min="0" step="0.01" placeholder="0.00">
                                                         <span class="input-group-text">$</span>
                                                     </div>
                                                     @error('prix')
                                                         <div class="invalid-feedback">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
// Initialize CKEditor for Arabic description
    ClassicEditor
        .create(document.querySelector('#description_ar'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
            language: 'ar'
        })
        .catch(error => {
            console.error(error);
        });

    // Initialize CKEditor for English description
    ClassicEditor
        .create(document.querySelector('#description_en'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });
});
</script>
@endpush
