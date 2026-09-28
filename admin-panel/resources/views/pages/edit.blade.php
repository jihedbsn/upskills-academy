@extends('layouts.app')

@section('content')
<div class="container-fluid">
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
                            <h4 class="card-title mb-1">Edit page</h4>
                            <p class="text-white-50 mb-0">Edit the content of page "{{ $page->titre_en }}"</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <h6 class="mb-1">Validation errors</h6>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('pages.update', $page) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Arabic Content Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="globe" class="me-2"></i>
                                            Arabic Content
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="titre_ar" class="form-label">Arabic Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('titre_ar') is-invalid @enderror" 
                                                       id="titre_ar" name="titre_ar" value="{{ old('titre_ar', $page->titre_ar) }}" required>
                                                @error('titre_ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="meta_title_ar" class="form-label">Arabic Meta Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('meta_title_ar') is-invalid @enderror" 
                                                       id="meta_title_ar" name="meta_title_ar" value="{{ old('meta_title_ar', $page->meta_title_ar) }}" required>
                                                @error('meta_title_ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                                                                         <div class="col-12 mb-3">
                                                 <label for="description_ar" class="form-label">Arabic Description <span class="text-danger">*</span></label>
                                                 <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                                            id="description_ar" name="description_ar" required>{{ old('description_ar', $page->description_ar) }}</textarea>
                                                 @error('description_ar')
                                                     <div class="invalid-feedback">{{ $message }}</div>
                                                 @enderror
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- English Content Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="globe" class="me-2"></i>
                                            English Content
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="titre_en" class="form-label">English Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('titre_en') is-invalid @enderror" 
                                                       id="titre_en" name="titre_en" value="{{ old('titre_en', $page->titre_en) }}" required>
                                                @error('titre_en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="meta_title_en" class="form-label">English Meta Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('meta_title_en') is-invalid @enderror" 
                                                       id="meta_title_en" name="meta_title_en" value="{{ old('meta_title_en', $page->meta_title_en) }}" required>
                                                @error('meta_title_en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                                                                         <div class="col-12 mb-3">
                                                 <label for="description_en" class="form-label">English Description <span class="text-danger">*</span></label>
                                                 <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                                            id="description_en" name="description_en" required>{{ old('description_en', $page->description_en) }}</textarea>
                                                 @error('description_en')
                                                     <div class="invalid-feedback">{{ $message }}</div>
                                                 @enderror
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="settings" class="me-2"></i>
                                            Settings
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="slug" class="form-label">Slug (URL)</label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                       id="slug" name="slug" value="{{ old('slug', $page->slug) }}" 
                                                       placeholder="Leave empty to generate automatically">
                                                <div class="form-text">Page URL (e.g: about-us, contact)</div>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                           {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        Active page
                                                    </label>
                                                </div>
                                                <div class="form-text">Determines if the page is publicly visible</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Page Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="info" class="me-2"></i>
                                            Page Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Page ID</label>
                                                <input type="text" class="form-control" value="{{ $page->id }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Created on</label>
                                                <input type="text" class="form-control" value="{{ $page->created_at->format('d/m/Y H:i') }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Modified on</label>
                                                <input type="text" class="form-control" value="{{ $page->updated_at->format('d/m/Y H:i') }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Page URL</label>
                                                <input type="text" class="form-control" value="/pages/{{ $page->slug }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Back to list
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pages.show', $page) }}" class="btn btn-info">
                                            <i data-feather="eye" class="me-2"></i>
                                            View page
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i data-feather="save" class="me-2"></i>
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modern-alert {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #d8d6de;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
}

.form-check-input:checked {
    background-color: #7367f0;
    border-color: #7367f0;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-warning {
    background-color: #ff9f43;
    border-color: #ff9f43;
}

.btn-warning:hover {
    background-color: #f39c12;
    border-color: #f39c12;
}
</style>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<script>
// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de CKEditor pour la description arabe
    ClassicEditor
        .create(document.querySelector('#description_ar'), {
            language: 'ar',
            direction: 'rtl',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .then(editor => {
            // Mettre à jour le textarea quand le contenu change
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                const textarea = document.querySelector('#description_ar');
                textarea.value = data;
            });
        })
        .catch(error => {
            console.error('Arabic CKEditor error:', error);
        });

    // Initialisation de CKEditor pour la description anglaise
    ClassicEditor
        .create(document.querySelector('#description_en'), {
            language: 'en',
            direction: 'ltr',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .then(editor => {
            // Mettre à jour le textarea quand le contenu change
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                const textarea = document.querySelector('#description_en');
                textarea.value = data;
            });
        })
        .catch(error => {
            console.error('English CKEditor error:', error);
        });

    // Génération automatique du slug
    document.getElementById('titre_en').addEventListener('input', function() {
        const slugField = document.getElementById('slug');
        if (slugField.value === '') {
            slugField.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
        }
    });

    // Gestion de la soumission du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        // S'assurer que les champs CKEditor sont remplis
        const descriptionAr = document.querySelector('#description_ar');
        const descriptionEn = document.querySelector('#description_en');
        
        if (descriptionAr.value.trim() === '') {
            e.preventDefault();
            alert('Please fill in the Arabic description');
            return false;
        }
        
        if (descriptionEn.value.trim() === '') {
            e.preventDefault();
            alert('Please fill in the English description');
            return false;
        }
    });
});
</script>
@endsection
