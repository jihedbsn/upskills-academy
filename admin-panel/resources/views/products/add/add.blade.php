@extends('layouts.app')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Styles pour Select2 */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9d9d9;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #6e6b7b;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d9d9d9;
        border-radius: 4px;
        padding: 8px 12px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: white;
    }

    .select2-dropdown {
        border: 1px solid #d9d9d9;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .select2-container--default .select2-results__option {
        padding: 8px 12px;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f8f9fa;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
    <h1 class="page-title">Add a New Product</h1>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" id="submitFormBtn">
                <i data-feather="save" class="me-2"></i>
                Save Product
            </button>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <!-- Les toasts seront ajoutés ici dynamiquement -->
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <div>
                    <h4 class="card-title mb-1">Create a Complete Product</h4>
                    <p class="text-white-50 mb-0">Use the rich text editor CKEditor to create detailed descriptions</p>
                </div>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger modern-alert">
                    <div class="d-flex align-items-center">
                        <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Erreurs de validation</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" id="productForm" enctype="multipart/form-data">
                @csrf

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="formTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="public-tab" data-bs-toggle="tab" data-bs-target="#public" type="button" role="tab">
                            <i data-feather="globe" class="me-2"></i>
                            <span class="d-none d-md-inline">Section Publique</span>
                            <span class="d-md-none">Public</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab">
                            <i data-feather="database" class="me-2"></i>
                            <span class="d-none d-md-inline">Données</span>
                            <span class="d-md-none">Data</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="types-tab" data-bs-toggle="tab" data-bs-target="#types" type="button" role="tab">
                            <i data-feather="tag" class="me-2"></i>
                            <span class="d-none d-md-inline">Types</span>
                            <span class="d-md-none">Types</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button" role="tab">
                            <i data-feather="help-circle" class="me-2"></i>
                            <span class="d-none d-md-inline">Quizzes & Exams</span>
                            <span class="d-md-none">Quiz</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="study-tab" data-bs-toggle="tab" data-bs-target="#study" type="button" role="tab">
                            <i data-feather="book-open" class="me-2"></i>
                            <span class="d-none d-md-inline">Study Resources</span>
                            <span class="d-md-none">Study</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="formTabsContent">

                    <!-- Public Section -->
                    <div class="tab-pane fade show active" id="public" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i data-feather="globe" class="text-primary me-2"></i>
                                    <h5 class="mb-0">Contenu Public</h5>
                                    <span class="badge bg-primary ms-2">Multilingue</span>
                                </div>
                            </div>

                            <!-- Arabic Section -->
                            <div class="col-md-6">
                                <div class="card border-primary section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="flag" class="me-2"></i>
                                            <h6 class="mb-0">Arabic Section</h6>
                                            <span class="badge bg-light text-dark ms-auto">العربية</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Name (Arabic) *
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="arabic_name" class="form-control" value="{{ old('arabic_name') }}" required>
                                            <div class="invalid-feedback">
                                                Arabic name is required.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="link" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Slug (Arabic) *
                                            </label>
                                            <input type="text" name="arabic_slug" class="form-control" value="{{ old('arabic_slug') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">
                                                    <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    Short description (Arabic) *
                                                </label>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary sync-description"
                                                            data-source="#arabic_short_description"
                                                            data-target="#english_short_description"
                                                            title="Sync to English">
                                                        <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info preview-content"
                                                            data-field="#arabic_short_description"
                                                            data-title="Preview - Short description (Arabic)"
                                                            title="Prévisualiser">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <textarea name="arabic_short_description" id="arabic_short_description" class="ckeditor-field" required>{{ old('arabic_short_description') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="clipboard" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Exams description (Arabic) *
                                            </label>
                                            <textarea name="arabic_description_exams" id="arabic_description_exams" class="ckeditor-field" required>{{ old('arabic_description_exams') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="help-circle" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Quizzes description (Arabic) *
                                            </label>
                                            <textarea name="arabic_description_quizzes" id="arabic_description_quizzes" class="ckeditor-field" required>{{ old('arabic_description_quizzes') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Final exam description (Arabic)
                                            </label>
                                            <textarea name="arabic_description_final_exam" id="arabic_description_final_exam" class="ckeditor-field">{{ old('arabic_description_final_exam') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Video exam description (Arabic)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="arabic_description_video_exam" id="arabic_description_video_exam" class="ckeditor-field">{{ old('arabic_description_video_exam') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="briefcase" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Internship description (Arabic)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="arabic_description_stage" id="arabic_description_stage" class="ckeditor-field">{{ old('arabic_description_stage') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="book-open" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Case study description (Arabic)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="arabic_description_study_case" id="arabic_description_study_case" class="ckeditor-field">{{ old('arabic_description_study_case') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- English Section -->
                            <div class="col-md-6">
                                <div class="card border-success section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="flag" class="me-2"></i>
                                            <h6 class="mb-0">English Section</h6>
                                            <span class="badge bg-light text-dark ms-auto">English</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Name (English) *
                                            </label>
                                            <input type="text" name="english_name" class="form-control" value="{{ old('english_name') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="link" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Slug (English) *
                                            </label>
                                            <input type="text" name="english_slug" class="form-control" value="{{ old('english_slug') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">
                                                    <i data-feather="file-text" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    Short description (English) *
                                                </label>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary sync-description"
                                                            data-source="#english_short_description"
                                                            data-target="#arabic_short_description"
                                                            title="Sync to Arabic">
                                                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info preview-content"
                                                            data-field="#english_short_description"
                                                            data-title="Preview - Short description (English)"
                                                            title="Prévisualiser">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <textarea name="english_short_description" id="english_short_description" class="ckeditor-field" required>{{ old('english_short_description') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="clipboard" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Exams description (English) *
                                            </label>
                                            <textarea name="english_description_exams" id="english_description_exams" class="ckeditor-field" required>{{ old('english_description_exams') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="help-circle" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Quizzes description (English) *
                                            </label>
                                            <textarea name="english_description_quizzes" id="english_description_quizzes" class="ckeditor-field" required>{{ old('english_description_quizzes') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Final exam description (English)
                                            </label>
                                            <textarea name="english_description_final_exam" id="english_description_final_exam" class="ckeditor-field">{{ old('english_description_final_exam') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Video exam description (English)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="english_description_video_exam" id="english_description_video_exam" class="ckeditor-field">{{ old('english_description_video_exam') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="briefcase" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Internship description (English)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="english_description_stage" id="english_description_stage" class="ckeditor-field">{{ old('english_description_stage') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="book-open" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Case study description (English)
                                                <span class="text-muted">(Optionnel)</span>
                                            </label>
                                            <textarea name="english_description_study_case" id="english_description_study_case" class="ckeditor-field">{{ old('english_description_study_case') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Section -->
                    <div class="tab-pane fade" id="data" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i data-feather="database" class="text-info me-2"></i>
                                    <h5 class="mb-0">Données du Produit</h5>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="hash" class="me-1" style="width: 14px; height: 14px;"></i>
                                        ID du produit
                                        <span class="text-muted">(Auto-généré)</span>
                                    </label>
                                    <input type="text" class="form-control" value="Auto-généré" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="tag" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Category *
                                    </label>
                                    <select name="categories_id" class="form-select" required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('categories_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="user" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Teacher *
                                    </label>
                                    <select name="teacher_id" class="form-select" required>
                                        <option value="">Select a teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="globe" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Country *
                                    </label>
                                    <select name="country_id" class="form-select" required>
                                        <option value="">Select a country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Certificate
                                        <span class="text-muted">(Optional)</span>
                                    </label>
                                    <select name="certif_id" class="form-select">
                                        <option value="">Select a certificate</option>
                                        @foreach($certifs as $certif)
                                            <option value="{{ $certif->id }}" {{ old('certif_id') == $certif->id ? 'selected' : '' }}>
                                                {{ $certif->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="settings" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Certificate Generation Mode
                                        <span class="text-muted">(Optional)</span>
                                    </label>
                                    <select name="certificate_generation_mode" class="form-select">
                                        <option value="manual" {{ old('certificate_generation_mode', 'manual') == 'manual' ? 'selected' : '' }}>
                                            Manual - Admin will be notified to generate certificate
                                        </option>
                                        <option value="automatic" {{ old('certificate_generation_mode') == 'automatic' ? 'selected' : '' }}>
                                            Automatic - Certificate generates automatically and emails student
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Choose how certificates are generated when students complete this course.
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Period *
                                    </label>
                                    <input type="text" name="period" class="form-control" value="{{ old('period') }}" placeholder="Ex: 3 mois, 6 semaines" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Points *
                                    </label>
                                    <input type="number" name="point" class="form-control" value="{{ old('point', 0) }}" min="0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="dollar-sign" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Prix
                                        <span class="text-muted">(Optionnel)</span>
                                    </label>
                                    <input type="number" step="0.01" name="prix" class="form-control" value="{{ old('prix') }}" min="0" placeholder="0.00">
                                </div>

                                <!-- Course Visibility Toggle -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_listed" name="is_listed" value="1" {{ old('is_listed', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_listed">
                                            <i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>
                                            List course in catalog
                                        </label>
                                    </div>
                                    <small class="text-muted">When unchecked, course won't appear in user app catalog but enrolled students can still access it from their dashboard</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="repeat" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Max Exam Attempts
                                        <span class="text-muted">(Default: 3)</span>
                                    </label>
                                    <input type="number" name="max_exam_attempts" class="form-control" value="{{ old('max_exam_attempts', 3) }}" min="1" max="100" placeholder="3">
                                    <div class="form-text">
                                        <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Maximum number of exam attempts before requiring renewal
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="dollar-sign" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Renewal Price ($)
                                        <span class="text-muted">(Default: $50.00)</span>
                                    </label>
                                    <input type="number" step="0.01" name="renewal_price" class="form-control" value="{{ old('renewal_price', 50.00) }}" min="0" placeholder="50.00">
                                    <div class="form-text">
                                        <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Price charged for exam renewal after exceeding attempts
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="iscach" id="iscach" value="1" {{ old('iscach') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="iscach">
                                            <i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Visible
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="image" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Image principale *
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                    <div class="invalid-feedback">
                                        L'image principale est obligatoire.
                                    </div>
                                    <div class="form-text">
                                        <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Formats acceptés : JPEG, PNG, JPG, GIF, WebP (max 50MB)
                                    </div>
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i data-feather="eye" class="me-2"></i>
                                                    Image preview
                                                </h6>
                                            </div>
                                            <div class="card-body text-center">
                                                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                                <div class="mt-2">
                                                    <small class="text-muted" id="imageInfo"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Promotional video
                                    </label>
                                    <input type="file" name="video" class="form-control" accept="video/*">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="star" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Points promotionnels
                                    </label>
                                    <input type="number" name="promo_points" class="form-control" value="{{ old('promo_points', 0) }}" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Date de publication
                                    </label>
                                    <input type="date" name="published_at" class="form-control" value="{{ old('published_at') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Types Section -->
                    <div class="tab-pane fade" id="types" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i data-feather="tag" class="text-warning me-2"></i>
                                    <h5 class="mb-0">Course Types</h5>
                                </div>
                            </div>

                            <!-- Dates and Information -->
                            <div class="col-12 mb-4">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <h6 class="mb-0">Course Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        Start date
                                                    </label>
                                                    <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        End date
                                                    </label>
                                                    <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="book" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        Type de Cours *
                                                    </label>
                                                    <select name="type_course" class="form-select" required>
                                                        <option value="">Select a course type</option>
                                                        <option value="fa" {{ old('type_course') == 'fa' ? 'selected' : '' }}>Foundation English (FA)</option>
                                                        <option value="fi" {{ old('type_course') == 'fi' ? 'selected' : '' }}>Fluency &amp; Interaction (FI)</option>
                                                        <option value="pt" {{ old('type_course') == 'pt' ? 'selected' : '' }}>Proficiency Track (PT)</option>
                                                        <option value="autres" {{ old('type_course') == 'autres' ? 'selected' : '' }}>Autres</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="goverrnement" id="goverrnement" value="1" {{ old('goverrnement') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="goverrnement">
                                                            <i data-feather="shield" class="me-1" style="width: 14px; height: 14px;"></i>
                                                            Government training
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Types -->
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="settings" class="me-2"></i>
                                            <h6 class="mb-0">Course Types</h6>
                                            <span class="badge bg-primary ms-2">Configuration</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_classroom" name="is_classroom" value="1" {{ old('is_classroom') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_classroom">
                                                        <strong>On-site Training</strong>
                                                        <small class="d-block text-muted">Cours en salle de classe</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_zoom" name="is_zoom" value="1" {{ old('is_zoom') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_zoom">
                                                        <strong>Zoom Training</strong>
                                                        <small class="d-block text-muted">Cours en visioconférence</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" {{ old('is_online') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_online">
                                                        <strong>Online Training</strong>
                                                        <small class="d-block text-muted">Asynchronous online course</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <small class="text-muted">
                                                <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                                <strong>Note:</strong> You can select multiple course types for the same course.
                                                For example, a course can be available on-site AND online.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                    <!-- Quizzes and Exams Section -->
                    <div class="tab-pane fade" id="quizzes" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i data-feather="help-circle" class="text-info me-2"></i>
                                    <h5 class="mb-0">Quizzes and Exams</h5>
                                    <span class="badge bg-info ms-2">Gestion dynamique</span>
                                </div>
                            </div>

                            <!-- Configuration des options -->
                            <div class="col-12 mb-4">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="settings" class="me-2"></i>
                                            <h6 class="mb-0">Configuration des options</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_stage" name="is_stage" value="1">
                                                    <label class="form-check-label" for="is_stage">
                                                        <strong>Internship</strong>
                                                        <small class="d-block text-muted">This course includes a practical internship</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_exam_video" name="is_exam_video" value="1">
                                                    <label class="form-check-label" for="is_exam_video">
                                                        <strong>Video exam</strong>
                                                        <small class="d-block text-muted">This course requires a video exam</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Exam Types Section -->
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <hr>
                                                <h6 class="mb-3">
                                                    <i data-feather="clipboard" class="me-2"></i>
                                                    Exam Configuration
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="has_theoretical_exam" name="has_theoretical_exam" value="1">
                                                    <label class="form-check-label" for="has_theoretical_exam">
                                                        <strong>Theoretical Exam</strong>
                                                        <small class="d-block text-muted">Course has theoretical quiz-based exam</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="has_practical_exam" name="has_practical_exam" value="1" onchange="togglePracticalExamType()">
                                                    <label class="form-check-label" for="has_practical_exam">
                                                        <strong>Practical Exam</strong>
                                                        <small class="d-block text-muted">Course has practical training case exam</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Practical Exam Type -->
                                        <div class="row mt-3" id="practicalExamTypeSection" style="display: none;">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">
                                                    <i data-feather="settings" class="me-1"></i>
                                                    Practical Exam Type <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="practical_exam_type" name="practical_exam_type">
                                                    <option value="">Select type...</option>
                                                    <option value="online">Online (Student uploads video link)</option>
                                                    <option value="classroom">Classroom (Physical exam in gym)</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">
                                                    <i data-feather="file-text" class="me-1"></i>
                                                    Select Training Cases <span class="text-danger">*</span>
                                                </label>
                                                <div class="alert alert-info mb-2">
                                                    <i data-feather="info" style="width: 14px; height: 14px;"></i>
                                                    Students will be randomly assigned ONE case from your selection.
                                                    <a href="{{ route('training-cases.index') }}" target="_blank" class="alert-link">Manage Training Cases</a>
                                                </div>
                                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                                    @if($trainingCases->isEmpty())
                                                        <p class="text-muted text-center py-3">
                                                            No training cases available.
                                                            <a href="{{ route('training-cases.create') }}" target="_blank">Create one now</a>
                                                        </p>
                                                    @else
                                                        @foreach($trainingCases as $case)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input"
                                                                       type="checkbox"
                                                                       name="training_case_ids[]"
                                                                       value="{{ $case->id }}"
                                                                       id="training_case_{{ $case->id }}">
                                                                <label class="form-check-label" for="training_case_{{ $case->id }}">
                                                                    <strong>{{ $case->name }}</strong>
                                                                    <small class="d-block text-muted">{{ Str::limit($case->description, 80) }}</small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stage Documents Section -->
                                <div class="card border-primary mt-3" id="stageDocumentsSection" style="display: none;">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="file-text" class="me-2 text-primary"></i>
                                                <h6 class="mb-0">Documents de Stage</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-3">Note: Upload documents after creating the course in edit mode</p>

                                        <!-- Request Letter -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>خطاب الطلب (Official Request Letter)</strong></label>
                                            <div class="alert alert-info">
                                                <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Documents can be uploaded after the course is created.
                                            </div>
                                        </div>

                                        <!-- Evaluation Form -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>استمارة التقييم (Internship Evaluation Form)</strong></label>
                                            <div class="alert alert-info">
                                                <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Documents can be uploaded after the course is created.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Quiz -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="help-circle" class="me-2"></i>
                                                <h6 class="mb-0">Quiz</h6>
                                                <span class="badge bg-light text-dark ms-2">Questions</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-success" id="addQuizBtn">
                                                <i data-feather="plus" class="me-1"></i>
                                                Add a quiz
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="quizContainer">
                                            <!-- Quiz rows will be added here dynamically -->
                                            <div class="text-center py-4" id="noQuizMessage">
                                                <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">No quiz selected</p>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addQuizRow()">
                                                    <i data-feather="plus" class="me-1"></i>
                                                    Add the first quiz
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Exams Section -->
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="file-text" class="me-2"></i>
                                                <h6 class="mb-0">Exams</h6>
                                                <span class="badge bg-light text-dark ms-2">Assessments</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-warning" id="addExamBtn">
                                                <i data-feather="plus" class="me-1"></i>
                                                Add an exam
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="examContainer">
                                            <!-- Exam rows will be added here dynamically -->
                                            <div class="text-center py-4" id="noExamMessage">
                                                <i data-feather="file-text" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">Aucun examen sélectionné</p>
                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addExamRow()">
                                                    <i data-feather="plus" class="me-1"></i>
                                                    Ajouter le premier examen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Study Section -->
                    <div class="tab-pane fade" id="study" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i data-feather="book-open" class="text-info me-2"></i>
                                    <h5 class="mb-0">Study Resources</h5>
                                    <span class="badge bg-info ms-2">Gestion dynamique</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="book-open" class="me-2"></i>
                                                <h6 class="mb-0">Study Resources</h6>
                                                <span class="badge bg-light text-dark ms-2">Matériel pédagogique</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-info" id="addStudyBtn">
                                                <i data-feather="plus" class="me-1"></i>
                                                Ajouter une ressource
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="studyContainer">
                                            <!-- Les lignes de ressources d'étude seront ajoutées ici dynamiquement -->
                                            <div class="text-center py-4" id="noStudyMessage">
                                                <i data-feather="book-open" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">Aucune ressource d'étude sélectionnée</p>
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="addStudyRow()">
                                                    <i data-feather="plus" class="me-1"></i>
                                                    Ajouter la première ressource
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        <i data-feather="arrow-left" class="me-2"></i>
                        Retour
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="me-2"></i>
                        Enregistrer le Produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration CKEditor pour l'arabe (RTL)
    const arabicConfig = {
        language: 'ar',
        direction: 'rtl',
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'link', '|',
            'bulletedList', 'numberedList', '|',
            'indent', 'outdent', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraphe', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Titre 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Titre 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Titre 3', class: 'ck-heading_heading3' }
            ]
        }
    };

    // Configuration CKEditor pour l'anglais (LTR)
    const englishConfig = {
        language: 'en',
        direction: 'ltr',
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'link', '|',
            'bulletedList', 'numberedList', '|',
            'indent', 'outdent', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        }
    };

    // Stocker les instances CKEditor
    const ckeditorInstances = {};

    // Initialiser les éditeurs arabes
    const arabicEditors = [
        'arabic_short_description',
        'arabic_description_exams',
        'arabic_description_quizzes',
        'arabic_description_final_exam',
        'arabic_description_video_exam',
        'arabic_description_stage',
        'arabic_description_study_case'
    ];

    arabicEditors.forEach(function(editorId) {
        const element = document.querySelector('#' + editorId);
        if (element) {
            ClassicEditor
                .create(element, arabicConfig)
                .then(editor => {
                    // Stocker l'instance
                    ckeditorInstances[editorId] = editor;

                    // Sauvegarder automatiquement le contenu
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        element.value = data;
                        // Mettre à jour la progression après changement
                        setTimeout(updateTabProgress, 100);
                    });

                    console.log(`✅ CKEditor initialisé pour: ${editorId}`);
                })
                .catch(error => {
                    console.error(`❌ Erreur CKEditor pour ${editorId}:`, error);
                    // Marquer comme non disponible pour éviter les erreurs
                    ckeditorInstances[editorId] = null;
                });
        } else {
            console.warn(`⚠️ Élément CKEditor non trouvé: ${editorId}`);
        }
    });

    // Initialiser les éditeurs anglais
    const englishEditors = [
        'english_short_description',
        'english_description_exams',
        'english_description_quizzes',
        'english_description_final_exam',
        'english_description_video_exam',
        'english_description_stage',
        'english_description_study_case',
        'online_description',
        'classroom_description'
    ];

    englishEditors.forEach(function(editorId) {
        const element = document.querySelector('#' + editorId);
        if (element) {
            ClassicEditor
                .create(element, englishConfig)
                .then(editor => {
                    // Stocker l'instance
                    ckeditorInstances[editorId] = editor;

                    // Sauvegarder automatiquement le contenu
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        element.value = data;
                        // Mettre à jour la progression après changement
                        setTimeout(updateTabProgress, 100);
                    });

                    console.log(`✅ CKEditor initialisé pour: ${editorId}`);
                })
                .catch(error => {
                    console.error(`❌ Erreur CKEditor pour ${editorId}:`, error);
                    // Marquer comme non disponible pour éviter les erreurs
                    ckeditorInstances[editorId] = null;
                });
        } else {
            console.warn(`⚠️ Élément CKEditor non trouvé: ${editorId}`);
        }
    });

    // Fonction pour vérifier que tous les éditeurs CKEditor sont initialisés
    function waitForCKEditorsToLoad() {
        return new Promise((resolve) => {
            const checkInterval = setInterval(() => {
                const allEditorsLoaded = arabicEditors.concat(englishEditors).every(editorId => {
                    return ckeditorInstances[editorId] !== undefined;
                });

                if (allEditorsLoaded) {
                    clearInterval(checkInterval);
                    console.log('✅ Tous les éditeurs CKEditor sont chargés');
                    resolve();
                }
            }, 100);

            // Timeout après 10 secondes
            setTimeout(() => {
                clearInterval(checkInterval);
                console.warn('⚠️ Timeout: Certains éditeurs CKEditor ne sont pas chargés');
                resolve();
            }, 10000);
        });
    }

    // Attendre que tous les éditeurs soient chargés avant d'initialiser le reste
    waitForCKEditorsToLoad().then(() => {
        // Mise à jour initiale de la progression
        updateTabProgress();
    });

    // Navigation des onglets avec indicateur de progression
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Réinitialiser les éditeurs lors du changement d'onglet
            setTimeout(() => {
                // Les éditeurs CKEditor se mettent à jour automatiquement
            }, 100);

            // Mettre à jour l'indicateur de progression
            updateTabProgress();
        });
    });

    // Fonction pour mettre à jour l'indicateur de progression des onglets
    function updateTabProgress() {
        const tabs = ['public', 'data', 'types', 'quizzes', 'study'];
        const completedTabs = [];

        tabs.forEach(tabId => {
            const tabContent = document.getElementById(tabId);
            if (tabContent) {
                const requiredFields = tabContent.querySelectorAll('[required]');
                let completedFields = 0;

                requiredFields.forEach(field => {
                    if (field.value.trim() !== '') {
                        completedFields++;
                    }
                });

                // Vérifier aussi les champs CKEditor
                const ckEditorFields = tabContent.querySelectorAll('.ckeditor-field');
                ckEditorFields.forEach(field => {
                    const editorId = field.id;
                    if (ckeditorInstances[editorId]) {
                        try {
                            const content = ckeditorInstances[editorId].getData().trim();
                            if (content !== '') {
                                completedFields++;
                            }
                        } catch (error) {
                            console.warn(`Error getting CKEditor content for ${editorId}:`, error);
                        }
                    }
                });

                const progress = requiredFields.length > 0 ? (completedFields / requiredFields.length) * 100 : 100;

                if (progress >= 100) {
                    completedTabs.push(tabId);
                }

                // Mettre à jour l'icône de l'onglet
                const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                if (tabButton) {
                    const icon = tabButton.querySelector('i');
                    if (progress >= 100) {
                        icon.setAttribute('data-feather', 'check-circle');
                        tabButton.classList.add('text-success');
                    } else if (progress > 0) {
                        icon.setAttribute('data-feather', 'clock');
                        tabButton.classList.add('text-warning');
                    } else {
                        icon.setAttribute('data-feather', tabId === 'public' ? 'globe' :
                                                      tabId === 'data' ? 'database' :
                                                      tabId === 'types' ? 'tag' :
                                                      tabId === 'quizzes' ? 'help-circle' : 'book-open');
                        tabButton.classList.remove('text-success', 'text-warning');
                    }

                    // Réinitialiser Feather Icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }
            }
        });

        // Mettre à jour le bouton de soumission
        const submitBtn = document.getElementById('submitFormBtn');
        if (submitBtn) {
            if (completedTabs.length === tabs.length) {
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-success');
                submitBtn.innerHTML = '<i data-feather="check-circle" class="me-2"></i>Prêt à créer le produit';
            } else {
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
                submitBtn.innerHTML = '<i data-feather="save" class="me-2"></i>Enregistrer le Produit';
            }

            // Réinitialiser Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    }

    // Validation du formulaire
    document.getElementById('productForm').addEventListener('submit', function(e) {
        // Validation basique
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });

    // Gestion automatique des slugs
    function generateSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Auto-slug pour l'arabe
    const arabicNameInput = document.querySelector('input[name="arabic_name"]');
    const arabicSlugInput = document.querySelector('input[name="arabic_slug"]');

    if (arabicNameInput && arabicSlugInput) {
        arabicNameInput.addEventListener('input', function() {
            arabicSlugInput.value = generateSlug(this.value);
        });
    }

    // Auto-slug pour l'anglais
    const englishNameInput = document.querySelector('input[name="english_name"]');
    const englishSlugInput = document.querySelector('input[name="english_slug"]');

    if (englishNameInput && englishSlugInput) {
        englishNameInput.addEventListener('input', function() {
            englishSlugInput.value = generateSlug(this.value);
        });
    }

    // Synchronisation des descriptions entre arabe et anglais
    const syncButtons = document.querySelectorAll('.sync-description');
    syncButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sourceField = this.getAttribute('data-source');
            const targetField = this.getAttribute('data-target');

            // Extraire les IDs des champs
            const sourceId = sourceField.replace('#', '');
            const targetId = targetField.replace('#', '');

            if (ckeditorInstances[sourceId] && ckeditorInstances[targetId]) {
                // Synchronisation entre éditeurs CKEditor
                const content = ckeditorInstances[sourceId].getData();
                ckeditorInstances[targetId].setData(content);
                console.log(`✅ Contenu synchronisé de ${sourceId} vers ${targetId}`);
            } else if (ckeditorInstances[sourceId]) {
                // Source est CKEditor, target est un champ normal
                const content = ckeditorInstances[sourceId].getData();
                const targetElement = document.querySelector(targetField);
                if (targetElement) {
                    targetElement.value = content;
                }
            } else if (ckeditorInstances[targetId]) {
                // Source est un champ normal, target est CKEditor
                const sourceElement = document.querySelector(sourceField);
                if (sourceElement) {
                    ckeditorInstances[targetId].setData(sourceElement.value);
                }
            } else {
                // Synchronisation entre champs normaux
                const sourceElement = document.querySelector(sourceField);
                const targetElement = document.querySelector(targetField);
                if (sourceElement && targetElement) {
                    targetElement.value = sourceElement.value;
                }
            }

            // Notification de synchronisation
            showNotification('Contenu synchronisé avec succès !', 'success');
        });
    });

    // Fonction de filtrage des quiz et examens
    function filterQuizzes(section) {
        const searchTerm = document.getElementById(section + 'Search').value.toLowerCase();
        const items = document.querySelectorAll('.' + section + '-item');

        items.forEach(item => {
            const name = item.dataset.name;
            if (name.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Événements de recherche en temps réel
    const quizSearchElement = document.getElementById('quizSearch');
    const examSearchElement = document.getElementById('examSearch');

    if (quizSearchElement) {
        quizSearchElement.addEventListener('input', function() {
            filterQuizzes('quiz');
        });
    }

    if (examSearchElement) {
        examSearchElement.addEventListener('input', function() {
            filterQuizzes('exam');
        });
    }

    // Fonction pour afficher les notifications
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Prévisualisation en temps réel
    const previewButtons = document.querySelectorAll('.preview-content');
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fieldId = this.getAttribute('data-field');
            const fieldIdClean = fieldId.replace('#', '');

            if (ckeditorInstances[fieldIdClean]) {
                const content = ckeditorInstances[fieldIdClean].getData();
                showPreview(content, this.getAttribute('data-title') || 'Prévisualisation');
            } else {
                const editor = document.querySelector(fieldId);
                if (editor) {
                    showPreview(editor.value, this.getAttribute('data-title') || 'Prévisualisation');
                }
            }
        });
    });

    function showPreview(content, title) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="border rounded p-3 bg-light">
                            ${content}
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();

        modal.addEventListener('hidden.bs.modal', function() {
            modal.remove();
        });
    }

    // ===== GESTION DES QUIZ ET EXAMENS =====

    // Variables pour les quiz et examens
    let quizIndex = 0;
    let examIndex = 0;

    // Données des quiz et examens disponibles (à récupérer depuis le serveur)
    const availableQuizzes = @json($quizzes->filter(function($quiz) { return stripos($quiz->type->titre, 'quiz') !== false; })->values());
    const availableExams = @json($quizzes->filter(function($quiz) { return stripos($quiz->type->titre, 'exam') !== false; })->values());

    // Debug: Vérifier les données reçues
    console.log('🔍 availableQuizzes:', availableQuizzes);
    console.log('🔍 availableExams:', availableExams);
    console.log('🔍 Type de availableQuizzes:', typeof availableQuizzes);
    console.log('🔍 Type de availableExams:', typeof availableExams);
    console.log('🔍 availableQuizzes est un tableau?', Array.isArray(availableQuizzes));
    console.log('🔍 availableExams est un tableau?', Array.isArray(availableExams));

    // Fonction pour ajouter une ligne de quiz
    window.addQuizRow = function() {
        const container = document.getElementById('quizContainer');
        const noQuizMessage = document.getElementById('noQuizMessage');

        // Vérifier que availableQuizzes est un tableau
        if (!Array.isArray(availableQuizzes)) {
            console.error('❌ availableQuizzes n\'est pas un tableau:', availableQuizzes);
            alert('Erreur: Impossible de charger les quiz disponibles');
            return;
        }

        // Masquer le message "aucun quiz"
        if (noQuizMessage) {
            noQuizMessage.style.display = 'none';
        }

        const newRow = document.createElement('div');
        newRow.className = 'quiz-row border rounded p-3 mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Sélectionner un Quiz</label>
                    <select name="quiz_ids[]" class="form-select quiz-select" required>
                        <option value="">Choisir un quiz...</option>
                        ${availableQuizzes.map(quiz => `
                            <option value="${quiz.id}" data-name-ar="${quiz.name_ar}" data-name-en="${quiz.name_en}" data-score="${quiz.score}" data-type="${quiz.type?.titre || ''}">
                                ${quiz.name_ar} - ${quiz.name_en} (${quiz.score}/100)
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nombre de questions</label>
                    <input type="number" name="quiz_nb_questions[]" class="form-control quiz-nb-question" value="10" min="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Score</label>
                    <input type="number" name="quiz_scores[]" class="form-control quiz-score" value="50" min="0" max="100">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger remove-quiz">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted quiz-details"></small>
                </div>
            </div>
        `;

        container.appendChild(newRow);
        quizIndex++;

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        const scoreInput = newRow.querySelector('.quiz-score');
        const nbInput = newRow.querySelector('.quiz-nb-question');
        if (scoreInput) {
            scoreInput.value = 50;
            scoreInput.dataset.userEdited = 'false';
            scoreInput.addEventListener('input', () => {
                scoreInput.dataset.userEdited = 'true';
            });
        }
        if (nbInput) {
            nbInput.value = 10;
        }

        // Ajouter l'événement de suppression
        newRow.querySelector('.remove-quiz').addEventListener('click', function() {
            newRow.remove();
            checkQuizContainer();
        });

        // Ajouter l'événement de changement de sélection
        newRow.querySelector('.quiz-select').addEventListener('change', function() {
            if (scoreInput) {
                scoreInput.dataset.userEdited = 'false';
            }
            updateQuizDetails(newRow);
            preventDuplicateSelection(this);
            reactivateOptions(this);
        });

        // Réinitialiser Select2 pour inclure le nouveau select
        setTimeout(() => {
            reinitializeAllSelect2();
        }, 100);

        console.log('✅ Ligne de quiz ajoutée');
    };

    // Fonction pour ajouter une ligne d'examen
    window.addExamRow = function() {
        const container = document.getElementById('examContainer');
        const noExamMessage = document.getElementById('noExamMessage');

        // Vérifier que availableExams est un tableau
        if (!Array.isArray(availableExams)) {
            console.error('❌ availableExams n\'est pas un tableau:', availableExams);
            alert('Erreur: Impossible de charger les examens disponibles');
            return;
        }

        // Masquer le message "aucun examen"
        if (noExamMessage) {
            noExamMessage.style.display = 'none';
        }

        const newRow = document.createElement('div');
        newRow.className = 'exam-row border rounded p-3 mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Sélectionner un Examen</label>
                    <select name="exam_ids[]" class="form-select exam-select" required>
                        <option value="">Choisir un examen...</option>
                        ${availableExams.map(exam => `
                            <option value="${exam.id}" data-name-ar="${exam.name_ar}" data-name-en="${exam.name_en}" data-score="${exam.score}">
                                ${exam.name_ar} - ${exam.name_en} (${exam.score}/100)
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nombre de questions</label>
                    <input type="number" name="exam_nb_questions[]" class="form-control exam-nb-question" value="10" min="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Score</label>
                    <input type="number" name="exam_scores[]" class="form-control exam-score" value="50" min="0" max="100">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger remove-exam">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input exam-use-own-checkbox" type="checkbox" name="exam_use_own_questions_temp_${examIndex}" value="1" id="exam_use_own_${examIndex}" data-exam-index="${examIndex}">
                        <label class="form-check-label" for="exam_use_own_${examIndex}">
                            Use Exam's Own Questions (if unchecked, will fetch from course quizzes)
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted exam-details"></small>
                </div>
            </div>
        `;

        container.appendChild(newRow);
        examIndex++;

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        const examScoreInput = newRow.querySelector('.exam-score');
        const examNbInput = newRow.querySelector('.exam-nb-question');
        if (examScoreInput) {
            examScoreInput.value = 50;
            examScoreInput.dataset.userEdited = 'false';
            examScoreInput.addEventListener('input', () => {
                examScoreInput.dataset.userEdited = 'true';
            });
        }
        if (examNbInput) {
            examNbInput.value = 10;
        }

        // Ajouter l'événement de suppression
        newRow.querySelector('.remove-exam').addEventListener('click', function() {
            newRow.remove();
            checkExamContainer();
        });

        // Ajouter l'événement de changement de sélection
        newRow.querySelector('.exam-select').addEventListener('change', function() {
            // Update checkbox name with actual exam ID
            const checkbox = newRow.querySelector('.exam-use-own-checkbox');
            if (checkbox && this.value) {
                checkbox.setAttribute('name', `exam_use_own_questions[${this.value}]`);
            }
            if (examScoreInput) {
                examScoreInput.dataset.userEdited = 'false';
            }
            updateExamDetails(newRow);
            preventDuplicateSelection(this);
            reactivateOptions(this);
        });

        // Réinitialiser Select2 pour inclure le nouveau select d'examen
        setTimeout(() => {
            reinitializeAllSelect2();
        }, 100);

        console.log('✅ Ligne d\'examen ajoutée');
    };

    // Fonction pour mettre à jour les détails d'un quiz
    function updateQuizDetails(row) {
        const select = row.querySelector('.quiz-select');
        const scoreInput = row.querySelector('.quiz-score');
        const detailsDiv = row.querySelector('.quiz-details');

        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const nameAr = selectedOption.getAttribute('data-name-ar');
            const nameEn = selectedOption.getAttribute('data-name-en');
            const score = selectedOption.getAttribute('data-score');

            if (scoreInput && scoreInput.dataset.userEdited !== 'true') {
                scoreInput.value = 50;
            }
            detailsDiv.innerHTML = `<strong>${nameAr}</strong> - ${nameEn}`;
        } else {
            if (scoreInput) {
                if (scoreInput.dataset) {
                    scoreInput.dataset.userEdited = 'false';
                }
                scoreInput.value = 50;
            }
            detailsDiv.innerHTML = '';
        }
    }

    // Fonction pour mettre à jour les détails d'un examen
    function updateExamDetails(row) {
        const select = row.querySelector('.exam-select');
        const scoreInput = row.querySelector('.exam-score');
        const detailsDiv = row.querySelector('.exam-details');

        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const nameAr = selectedOption.getAttribute('data-name-ar');
            const nameEn = selectedOption.getAttribute('data-name-en');
            if (scoreInput && scoreInput.dataset.userEdited !== 'true') {
                scoreInput.value = 50;
            }
            detailsDiv.innerHTML = `<strong>${nameAr}</strong> - ${nameEn}`;
        } else {
            if (scoreInput) {
                if (scoreInput.dataset) {
                    scoreInput.dataset.userEdited = 'false';
                }
                scoreInput.value = 50;
            }
            detailsDiv.innerHTML = '';
        }
    }

    // Fonction pour vérifier si le conteneur de quiz est vide
    function checkQuizContainer() {
        const container = document.getElementById('quizContainer');
        const noQuizMessage = document.getElementById('noQuizMessage');
        const quizRows = container.querySelectorAll('.quiz-row');

        if (quizRows.length === 0 && noQuizMessage) {
            noQuizMessage.style.display = 'block';
        }
    }

    // Fonction pour vérifier si le conteneur d'examens est vide
    function checkExamContainer() {
        const container = document.getElementById('examContainer');
        const noExamMessage = document.getElementById('noExamMessage');
        const examRows = container.querySelectorAll('.exam-row');

        if (examRows.length === 0 && noExamMessage) {
            noExamMessage.style.display = 'block';
        }
    }

    // Événements pour les boutons d'ajout
    const addQuizBtn = document.getElementById('addQuizBtn');
    const addExamBtn = document.getElementById('addExamBtn');

    if (addQuizBtn) {
        addQuizBtn.addEventListener('click', addQuizRow);
    }

    if (addExamBtn) {
        addExamBtn.addEventListener('click', addExamRow);
    }

    console.log('🎉 Gestion des quiz et examens initialisée');

    // ===== GESTION DES RESSOURCES D'ÉTUDE =====

    // Variables pour les ressources d'étude
    let studyIndex = 0;

    // Données des ressources disponibles
    const availableResources = @json($resources);

    // Debug: Vérifier les données reçues
    console.log('🔍 availableResources:', availableResources);
    console.log('🔍 Type de availableResources:', typeof availableResources);
    console.log('🔍 availableResources est un tableau?', Array.isArray(availableResources));

    // Fonction pour ajouter une ligne de ressource d'étude
    window.addStudyRow = function() {
        const container = document.getElementById('studyContainer');
        const noStudyMessage = document.getElementById('noStudyMessage');

        // Vérifier que availableResources est un tableau
        if (!Array.isArray(availableResources)) {
            console.error('❌ availableResources n\'est pas un tableau:', availableResources);
            alert('Erreur: Impossible de charger les ressources disponibles');
            return;
        }

        // Masquer le message "aucune ressource"
        if (noStudyMessage) {
            noStudyMessage.style.display = 'none';
        }

        const newRow = document.createElement('div');
        newRow.className = 'study-row border rounded p-3 mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-10">
                    <label class="form-label">Sélectionner une Ressource</label>
                    <select name="study_resources[${studyIndex}][resource_id]" class="form-select study-resource-select" required>
                        <option value="">Choisir une ressource...</option>
                        ${availableResources.map(resource => `
                            <option value="${resource.id}" data-name-ar="${resource.name_ar}" data-name-en="${resource.name_en}" data-type="${resource.type}">
                                ${resource.name_ar} - ${resource.name_en} (${resource.type})
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger remove-study">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted study-details"></small>
                </div>
            </div>
        `;

        container.appendChild(newRow);
        studyIndex++;

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Ajouter l'événement de suppression
        newRow.querySelector('.remove-study').addEventListener('click', function() {
            newRow.remove();
            checkStudyContainer();
        });

        // Ajouter l'événement de changement de sélection
        newRow.querySelector('.study-resource-select').addEventListener('change', function() {
            updateStudyDetails(newRow);
            preventDuplicateSelection(this);
            reactivateOptions(this);
        });

        // Réinitialiser Select2 pour inclure le nouveau select de ressource
        setTimeout(() => {
            reinitializeAllSelect2();
        }, 100);

        console.log('✅ Ligne de ressource d\'étude ajoutée');
    };

    // Fonction pour mettre à jour les détails d'une ressource d'étude
    function updateStudyDetails(row) {
        const select = row.querySelector('.study-resource-select');
        const detailsDiv = row.querySelector('.study-details');

        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const nameAr = selectedOption.getAttribute('data-name-ar');
            const nameEn = selectedOption.getAttribute('data-name-en');
            const type = selectedOption.getAttribute('data-type');

            // Afficher les détails
            detailsDiv.innerHTML = `<strong>Nom (Arabe):</strong> ${nameAr} | <strong>Nom (Anglais):</strong> ${nameEn} | <strong>Type:</strong> ${type}`;
        } else {
            detailsDiv.innerHTML = '';
        }
    }

    // Fonction pour vérifier si le conteneur de ressources d'étude est vide
    function checkStudyContainer() {
        const container = document.getElementById('studyContainer');
        const noStudyMessage = document.getElementById('noStudyMessage');
        const studyRows = container.querySelectorAll('.study-row');

        if (studyRows.length === 0 && noStudyMessage) {
            noStudyMessage.style.display = 'block';
        }
    }

    // Événement pour le bouton d'ajout de ressource d'étude
    const addStudyBtn = document.getElementById('addStudyBtn');

    if (addStudyBtn) {
        addStudyBtn.addEventListener('click', addStudyRow);
    }

    console.log('🎉 Gestion des ressources d\'étude initialisée');

    // ===== SYSTÈME DE VALIDATION AVEC TOASTS =====

    // Fonction pour créer et afficher un toast
    function showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i data-feather="${type === 'success' ? 'check-circle' : type === 'danger' ? 'alert-triangle' : type === 'warning' ? 'alert-circle' : 'info'}" class="me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });

        toast.show();

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Supprimer le toast du DOM après qu'il soit caché
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }

    // Fonction pour valider un champ
    function validateField(field, fieldName) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');

        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return { valid: false, message: `Le champ "${fieldName}" est obligatoire.` };
        }

        // Validation spécifique pour les champs de type file
        if (field.type === 'file' && isRequired) {
            if (!field.files || field.files.length === 0) {
                field.classList.add('is-invalid');
                return { valid: false, message: `Le champ "${fieldName}" est obligatoire.` };
            }

            // Validation du type de fichier pour les images
            if (field.accept && field.accept.includes('image/*')) {
                const file = field.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    field.classList.add('is-invalid');
                    return { valid: false, message: `Le fichier "${file.name}" n'est pas un format d'image valide.` };
                }

                // Validation de la taille (50MB max)
                const maxSize = 50 * 1024 * 1024; // 50MB en bytes
                if (file.size > maxSize) {
                    field.classList.add('is-invalid');
                    return { valid: false, message: `Le fichier "${file.name}" est trop volumineux (max 50MB).` };
                }
            }
        }

        // Validation spécifique pour les emails
        if (field.type === 'email' && value && !isValidEmail(value)) {
            field.classList.add('is-invalid');
            return { valid: false, message: `L'email "${value}" n'est pas valide.` };
        }

        // Validation pour les nombres
        if (field.type === 'number' && value) {
            const numValue = parseFloat(value);
            if (isNaN(numValue) || numValue < 0) {
                field.classList.add('is-invalid');
                return { valid: false, message: `Le champ "${fieldName}" doit être un nombre positif.` };
            }
        }

        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        return { valid: true };
    }

    // Fonction pour valider un email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Fonction pour valider les champs CKEditor
    function validateCKEditor(editorId, fieldName) {
        const editor = ckeditorInstances[editorId];
        if (!editor) {
            console.warn(`CKEditor instance not found for: ${editorId}`);
            return { valid: true };
        }

        const content = editor.getData().trim();
        if (!content) {
            // Ajouter une classe d'erreur visuelle
            const editorElement = document.getElementById(editorId);
            if (editorElement) {
                const ckEditorContainer = editorElement.closest('.ck-editor');
                if (ckEditorContainer && ckEditorContainer.classList) {
                    ckEditorContainer.classList.add('border-danger');
                }
            }
            return { valid: false, message: `Le champ "${fieldName}" est obligatoire.` };
        }

        // Retirer la classe d'erreur
        const editorElement = document.getElementById(editorId);
        if (editorElement) {
            const ckEditorContainer = editorElement.closest('.ck-editor');
            if (ckEditorContainer && ckEditorContainer.classList) {
                ckEditorContainer.classList.remove('border-danger');
            }
        }

        return { valid: true };
    }

    // Fonction pour valider tout le formulaire
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Supprimer toutes les classes de validation précédentes
        document.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });

        // Validation des champs de base (Data section)
        const requiredFields = [
            { selector: 'select[name="categories_id"]', name: 'Catégorie' },
            { selector: 'select[name="teacher_id"]', name: 'Enseignant' },
            { selector: 'select[name="country_id"]', name: 'Pays' },
            { selector: 'input[name="period"]', name: 'Période' },
            { selector: 'input[name="point"]', name: 'Points' },
            { selector: 'input[name="image"]', name: 'Image principale' },
        ];

        requiredFields.forEach(field => {
            const element = document.querySelector(field.selector);
            if (element) {
                const validation = validateField(element, field.name);
                if (!validation.valid) {
                    isValid = false;
                    errors.push(validation.message);
                }
            }
        });

        // Validation des champs CKEditor (Section Public) - Dynamique
        const ckEditorFields = [
            { id: 'arabic_short_description', name: 'Description courte (Arabe)' },
            { id: 'arabic_description_exams', name: 'Description des examens (Arabe)' },
            { id: 'arabic_description_quizzes', name: 'Description des quiz (Arabe)' },
            { id: 'english_short_description', name: 'Description courte (Anglais)' },
            { id: 'english_description_exams', name: 'Description des examens (Anglais)' },
            { id: 'english_description_quizzes', name: 'Description des quiz (Anglais)' },
            { id: 'online_content', name: 'Contenu formation en ligne' },
            { id: 'classroom_content', name: 'Contenu formation en présentiel' },
        ];

        ckEditorFields.forEach(field => {
            // Vérifier que l'élément existe dans le DOM ET qu'il a l'attribut required
            const element = document.getElementById(field.id);
            if (element && element.hasAttribute('required')) {
                const validation = validateCKEditor(field.id, field.name);
                if (!validation.valid) {
                    isValid = false;
                    errors.push(validation.message);
                }
            }
        });

        // Validation des champs de métadonnées
        const metaFields = [
            { selector: 'input[name="online_meta_title"]', name: 'Titre SEO formation en ligne' },
            { selector: 'textarea[name="online_meta_keywords"]', name: 'Mots-clés SEO formation en ligne' },
            { selector: 'textarea[name="online_meta_description"]', name: 'Description SEO formation en ligne' },
            { selector: 'input[name="classroom_meta_title"]', name: 'Titre SEO formation en présentiel' },
            { selector: 'textarea[name="classroom_meta_keywords"]', name: 'Mots-clés SEO formation en présentiel' },
            { selector: 'textarea[name="classroom_meta_description"]', name: 'Description SEO formation en présentiel' },
        ];

        metaFields.forEach(field => {
            const element = document.querySelector(field.selector);
            if (element) {
                const validation = validateField(element, field.name);
                if (!validation.valid) {
                    isValid = false;
                    errors.push(validation.message);
                }
            }
        });

        // Validation des champs de base (noms et slugs)
        const basicFields = [
            { selector: 'input[name="arabic_name"]', name: 'Nom (Arabe)' },
            { selector: 'input[name="arabic_slug"]', name: 'Slug (Arabe)' },
            { selector: 'input[name="english_name"]', name: 'Nom (Anglais)' },
            { selector: 'input[name="english_slug"]', name: 'Slug (Anglais)' },
        ];

        basicFields.forEach(field => {
            const element = document.querySelector(field.selector);
            if (element) {
                const validation = validateField(element, field.name);
                if (!validation.valid) {
                    isValid = false;
                    errors.push(validation.message);
                }
            }
        });

        return { isValid, errors };
    }

    // Événement pour le bouton de soumission avec Sweet Alert
    const submitFormBtn = document.getElementById('submitFormBtn');
    const productForm = document.getElementById('productForm');

    if (submitFormBtn && productForm) {
        submitFormBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Valider le formulaire
            const validation = validateForm();

            if (!validation.isValid) {
                // Afficher les erreurs avec Sweet Alert et option "Enregistrer quand même"
                let errorMessage = `❌ ${validation.errors.length} erreur(s) de validation détectée(s):\n\n`;
                validation.errors.forEach((error, index) => {
                    errorMessage += `${index + 1}. ${error}\n`;
                });

                errorMessage += `\n⚠️ Le produit sera enregistré avec le statut "Brouillon" (breuillant = true)`;

                Swal.fire({
                    icon: 'warning',
                    title: 'Erreurs de validation',
                    text: errorMessage,
                    showCancelButton: true,
                    confirmButtonText: 'Corriger',
                    cancelButtonText: 'Enregistrer quand même',
                    confirmButtonColor: '#7367f0',
                    cancelButtonColor: '#ffc107',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Faire défiler vers le premier champ en erreur
                        const firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Enregistrer quand même avec breuillant = true
                        submitFormAjax(true); // true = force save with breuillant
                    }
                });

                return;
            }

            // Si tout est valide, demander confirmation avec Sweet Alert
            Swal.fire({
                title: 'Confirmer la création',
                text: 'Êtes-vous sûr de vouloir créer ce produit ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Oui, créer le produit',
                cancelButtonText: 'Annuler',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire via AJAX avec breuillant = false (tout est valide)
                    submitFormAjax(false);
                }
            });
        });
    }

    // Fonction pour soumettre le formulaire via AJAX
    function submitFormAjax(forceSave = false) {
        // Vérifier les valeurs dupliquées avant soumission
        const duplicateErrors = validateUniqueValues();

        if (duplicateErrors.length > 0) {
            // Essayer de nettoyer automatiquement les doublons
            const cleaned = cleanDuplicateValues();

            if (cleaned) {
                // Afficher un message que les doublons ont été nettoyés
                Swal.fire({
                    icon: 'info',
                    title: 'Doublons détectés',
                    text: 'Des valeurs dupliquées ont été détectées et automatiquement supprimées. Veuillez vérifier vos sélections.',
                    confirmButtonText: 'Continuer',
                    confirmButtonColor: '#007bff'
                }).then(() => {
                    // Relancer la soumission après nettoyage
                    submitFormAjax(forceSave);
                });
                return;
            } else {
                // Afficher les erreurs de doublons
                let errorMessage = `❌ ${duplicateErrors.length} valeur(s) dupliquée(s) détectée(s):\n\n`;
                duplicateErrors.forEach((error, index) => {
                    errorMessage += `${index + 1}. ${error}\n`;
                });
                errorMessage += `\n⚠️ Veuillez corriger ces doublons avant de continuer.`;

                Swal.fire({
                    icon: 'error',
                    title: 'Valeurs dupliquées',
                    text: errorMessage,
                    confirmButtonText: 'Corriger',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
        }

        // Afficher un loader avec message adapté
        const title = forceSave ? 'Enregistrement en cours...' : 'Création en cours...';
        const text = forceSave ? 'Enregistrement du produit en mode brouillon...' : 'Veuillez patienter pendant la création du produit';

        Swal.fire({
            title: title,
            text: text,
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Préparer les données du formulaire
        const formData = new FormData(productForm);

        // Ajouter le paramètre forceSave pour indiquer au serveur
        formData.append('force_save', forceSave ? '1' : '0');

        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);

        // Envoyer la requête AJAX
        fetch(productForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            const data = contentType.includes('application/json') ? await response.json().catch(() => ({})) : {};

            if (response.ok && data.success) {
                // Message adapté selon le mode de sauvegarde
                const title = forceSave ? 'Produit enregistré en brouillon !' : 'Succès !';
                const text = forceSave ?
                    'Le produit a été enregistré en mode brouillon. Vous pourrez le finaliser plus tard.' :
                    (data.message || 'Produit créé avec succès !');
                const icon = forceSave ? 'warning' : 'success';

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    confirmButtonText: 'OK',
                    confirmButtonColor: forceSave ? '#ffc107' : '#28a745',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = data.redirect || '/products';
                });
                return;
            }

            // Gestion des erreurs connues
            if (response.status === 422 && data && data.errors) {
                const firstError = Object.values(data.errors)[0];
                const msg = Array.isArray(firstError) ? firstError[0] : String(firstError);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreurs de validation',
                    text: msg || 'Veuillez vérifier les champs requis et réessayer.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            if (response.status === 419) {
                Swal.fire({
                    icon: 'error',
                    title: 'Session expirée',
                    text: 'La session CSRF a expiré. Rechargez la page et réessayez.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: (data && data.message) ? data.message : `Une erreur est survenue (code ${response.status}).`,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur de connexion',
                text: 'Une erreur est survenue lors de l\'envoi des données. Veuillez réessayer.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // Événements pour la validation en temps réel
    document.addEventListener('input', function(e) {
        const field = e.target;
        const fieldName = field.getAttribute('placeholder') || field.getAttribute('name') || 'Champ';

        if (field.hasAttribute('required')) {
            validateField(field, fieldName);
        }

        // Mettre à jour la progression des onglets
        updateTabProgress();
    });

    // Événement spécifique pour les champs de type file
    document.addEventListener('change', function(e) {
        const field = e.target;
        if (field.type === 'file' && field.hasAttribute('required')) {
            const fieldName = field.getAttribute('name') || 'Fichier';
            validateField(field, fieldName);
            updateTabProgress();

            // Gestion de l'aperçu pour le champ image
            if (field.name === 'image' && field.files && field.files[0]) {
                showImagePreview(field.files[0]);
            }
        }
    });

    // Fonction pour afficher l'aperçu de l'image
    function showImagePreview(file) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const imageInfo = document.getElementById('imageInfo');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imageInfo.textContent = `${file.name} (${formatFileSize(file.size)})`;
                preview.style.display = 'block';

                // Réinitialiser Feather Icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    // Fonction pour formater la taille du fichier
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Événements pour les champs CKEditor
    document.addEventListener('change', function(e) {
        const field = e.target;
        if (field.classList.contains('ckeditor-field')) {
            const fieldName = field.getAttribute('placeholder') || field.getAttribute('name') || 'Champ';
            validateCKEditor(field.id, fieldName);
        }

        // Mettre à jour la progression des onglets
        updateTabProgress();
    });

    // Initialiser Select2 pour toutes les sections
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        initializeAllSelect2();
    } else {
        console.warn('jQuery ou Select2 non disponible, initialisation différée');
        // Réessayer après un délai
        setTimeout(function() {
            if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                initializeAllSelect2();
            }
        }, 500);
    }

    console.log('🎉 Système de validation avec Sweet Alert et progression initialisé');
});

// Fonction pour initialiser Select2
function initializeSelect2() {
    // Vérifier si jQuery et Select2 sont disponibles
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.warn('jQuery ou Select2 non disponible');
        return;
    }

    // Initialiser Select2 pour tous les selects de quiz existants avec configuration basique
    $('.quiz-select').each(function() {
        // Vérifier si Select2 n'est pas déjà initialisé
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                placeholder: 'Rechercher et sélectionner un quiz...',
                allowClear: true,
                width: '100%'
            });
        }
    });
}

// Fonction pour initialiser Select2 pour les examens
function initializeExamSelect2() {
    // Vérifier si jQuery et Select2 sont disponibles
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.warn('jQuery ou Select2 non disponible pour les examens');
        return;
    }

    // Initialiser Select2 pour tous les selects d'examens existants
    $('.exam-select').each(function() {
        // Vérifier si Select2 n'est pas déjà initialisé
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                placeholder: 'Rechercher et sélectionner un examen...',
                allowClear: true,
                width: '100%'
            });
        }
    });
}

// Fonction pour réinitialiser Select2 pour les examens
function reinitializeExamSelect2() {
    // Détruire les instances Select2 existantes seulement si elles sont initialisées
    $('.exam-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });

    // Réinitialiser Select2
    initializeExamSelect2();
}

// Fonction pour initialiser Select2 pour les ressources d'étude
function initializeStudySelect2() {
    // Vérifier si jQuery et Select2 sont disponibles
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.warn('jQuery ou Select2 non disponible pour les ressources d\'étude');
        return;
    }

    // Initialiser Select2 pour tous les selects de ressources existants
    $('.study-resource-select').each(function() {
        // Vérifier si Select2 n'est pas déjà initialisé
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                placeholder: 'Rechercher et sélectionner une ressource...',
                allowClear: true,
                width: '100%'
            });
        }
    });
}

// Fonction unifiée pour initialiser tous les Select2
function initializeAllSelect2() {
    // Vérifier si jQuery et Select2 sont disponibles
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.warn('jQuery ou Select2 non disponible');
        return;
    }

    // Configuration commune pour tous les Select2
    const commonConfig = {
        placeholder: 'Rechercher et sélectionner...',
        allowClear: true,
        width: '100%'
    };

    // Initialiser tous les types de selects
    const selectTypes = [
        { selector: '.quiz-select', placeholder: 'Rechercher et sélectionner un quiz...' },
        { selector: '.exam-select', placeholder: 'Rechercher et sélectionner un examen...' },
        { selector: '.study-resource-select', placeholder: 'Rechercher et sélectionner une ressource...' }
    ];

    selectTypes.forEach(type => {
        $(type.selector).each(function() {
            // Vérifier si Select2 n'est pas déjà initialisé
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    ...commonConfig,
                    placeholder: type.placeholder
                });
                console.log(`✅ Select2 initialisé pour ${type.selector}`);
            }
        });
    });
}

// Fonction unifiée pour réinitialiser tous les Select2
function reinitializeAllSelect2() {
    // Détruire toutes les instances Select2 existantes
    const selectTypes = ['.quiz-select', '.exam-select', '.study-resource-select'];

    selectTypes.forEach(selector => {
        $(selector).each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });

    // Réinitialiser tous les Select2
    initializeAllSelect2();
}

// Fonction pour valider les valeurs dupliquées avant soumission
function validateUniqueValues() {
    const errors = [];

    // Vérifier les quiz dupliqués
    const quizValues = [];
    const quizSelects = document.querySelectorAll('.quiz-select');
    quizSelects.forEach((select, index) => {
        if (select.value && select.value !== '') {
            if (quizValues.includes(select.value)) {
                errors.push(`Quiz dupliqué détecté à la ligne ${index + 1}: ${select.options[select.selectedIndex].text}`);
            } else {
                quizValues.push(select.value);
            }
        }
    });

    // Vérifier les examens dupliqués
    const examValues = [];
    const examSelects = document.querySelectorAll('.exam-select');
    examSelects.forEach((select, index) => {
        if (select.value && select.value !== '') {
            if (examValues.includes(select.value)) {
                errors.push(`Examen dupliqué détecté à la ligne ${index + 1}: ${select.options[select.selectedIndex].text}`);
            } else {
                examValues.push(select.value);
            }
        }
    });

    // Vérifier les ressources dupliquées
    const resourceValues = [];
    const resourceSelects = document.querySelectorAll('.study-resource-select');
    resourceSelects.forEach((select, index) => {
        if (select.value && select.value !== '') {
            if (resourceValues.includes(select.value)) {
                errors.push(`Ressource dupliquée détectée à la ligne ${index + 1}: ${select.options[select.selectedIndex].text}`);
            } else {
                resourceValues.push(select.value);
            }
        }
    });

    return errors;
}

// Fonction pour nettoyer les valeurs dupliquées automatiquement
function cleanDuplicateValues() {
    let cleaned = false;

    // Nettoyer les quiz dupliqués
    const quizValues = [];
    const quizSelects = document.querySelectorAll('.quiz-select');
    quizSelects.forEach((select) => {
        if (select.value && select.value !== '') {
            if (quizValues.includes(select.value)) {
                select.value = '';
                select.dispatchEvent(new Event('change'));
                cleaned = true;
            } else {
                quizValues.push(select.value);
            }
        }
    });

    // Nettoyer les examens dupliqués
    const examValues = [];
    const examSelects = document.querySelectorAll('.exam-select');
    examSelects.forEach((select) => {
        if (select.value && select.value !== '') {
            if (examValues.includes(select.value)) {
                select.value = '';
                select.dispatchEvent(new Event('change'));
                cleaned = true;
            } else {
                examValues.push(select.value);
            }
        }
    });

    // Nettoyer les ressources dupliquées
    const resourceValues = [];
    const resourceSelects = document.querySelectorAll('.study-resource-select');
    resourceSelects.forEach((select) => {
        if (select.value && select.value !== '') {
            if (resourceValues.includes(select.value)) {
                select.value = '';
                select.dispatchEvent(new Event('change'));
                cleaned = true;
            } else {
                resourceValues.push(select.value);
            }
        }
    });

    return cleaned;
}

// Fonction pour empêcher la sélection de valeurs déjà utilisées
function preventDuplicateSelection(selectElement) {
    const currentValue = selectElement.value;
    const selectType = selectElement.classList.contains('quiz-select') ? 'quiz' :
                      selectElement.classList.contains('exam-select') ? 'exam' :
                      selectElement.classList.contains('study-resource-select') ? 'resource' : 'unknown';

    if (!currentValue) return;

    // Trouver tous les selects du même type
    const sameTypeSelects = document.querySelectorAll(`.${selectType}-select`);

    sameTypeSelects.forEach(select => {
        if (select !== selectElement && select.value === currentValue) {
            // Désactiver l'option dans les autres selects
            const option = select.querySelector(`option[value="${currentValue}"]`);
            if (option) {
                option.disabled = true;
                option.style.color = '#ccc';
            }
        }
    });
}

// Fonction pour réactiver les options quand une sélection est supprimée
function reactivateOptions(selectElement) {
    const selectType = selectElement.classList.contains('quiz-select') ? 'quiz' :
                      selectElement.classList.contains('exam-select') ? 'exam' :
                      selectElement.classList.contains('study-resource-select') ? 'resource' : 'unknown';

    // Trouver tous les selects du même type
    const sameTypeSelects = document.querySelectorAll(`.${selectType}-select`);
    const usedValues = [];

    // Collecter toutes les valeurs utilisées
    sameTypeSelects.forEach(select => {
        if (select.value) {
            usedValues.push(select.value);
        }
    });

    // Réactiver les options qui ne sont plus utilisées
    sameTypeSelects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.value && !usedValues.includes(option.value)) {
                option.disabled = false;
                option.style.color = '';
            }
        });
    });
}
</script>

<script>
// Toggle practical exam type dropdown
function togglePracticalExamType() {
    const checkbox = document.getElementById('has_practical_exam');
    const section = document.getElementById('practicalExamTypeSection');
    const select = document.getElementById('practical_exam_type');

    if (checkbox.checked) {
        section.style.display = 'block';
        select.required = true;
    } else {
        section.style.display = 'none';
        select.required = false;
        select.value = '';
    }
}

// Toggle stage documents section
function toggleStageDocuments() {
    const checkbox = document.getElementById('is_stage');
    const section = document.getElementById('stageDocumentsSection');

    if (checkbox && section) {
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePracticalExamType();
    toggleStageDocuments();

    // Add event listener to stage checkbox
    const stageCheckbox = document.getElementById('is_stage');
    if (stageCheckbox) {
        stageCheckbox.addEventListener('change', toggleStageDocuments);
    }
});
</script>

<style>
/* Styles pour le template personnalisé de Select2 */
.quiz-option {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
}

.quiz-option:last-child {
    border-bottom: none;
}

.quiz-name {
    font-weight: 500;
    color: #333;
    margin-bottom: 4px;
}

.quiz-meta {
    display: flex;
    gap: 8px;
    font-size: 0.85em;
}

.quiz-type {
    background-color: #e3f2fd;
    color: #1976d2;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
}

.quiz-score {
    background-color: #f3e5f5;
    color: #7b1fa2;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
}
</style>

<!-- Select2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection
