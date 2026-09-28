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
    <h1 class="page-title">Edit Product #{{ $product->id }}</h1>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" id="submitFormBtn">
                <i data-feather="save" class="me-2"></i>
                Update Product
            </button>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <!-- Toasts will be added here dynamically -->
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <div>
                    <h4 class="card-title mb-1">Edit a Complete Product</h4>
                    <p class="text-white-50 mb-0">Edit product information with the rich text editor CKEditor</p>
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

            <form method="POST" action="{{ route('products.update', $product->id) }}" id="productForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                            <span class="d-none d-md-inline">Data</span>
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
                            <span class="d-none d-md-inline">Quiz & Examens</span>
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
                                             <h6 class="mb-0">Section Arabe</h6>
                                             <span class="badge bg-light text-dark ms-auto">العربية</span>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Nom (Arabe) *
                                                <span class="text-danger">*</span>
                                             </label>
                                             <input type="text" name="arabic_name" class="form-control" value="{{ old('arabic_name', $product->variations->where('langue', 'ar')->first()->name ?? '') }}" required>
                                            <div class="invalid-feedback">
                                                Le nom en arabe est obligatoire.
                                            </div>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="link" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Slug (Arabe) *
                                             </label>
                                             <input type="text" name="arabic_slug" class="form-control" value="{{ old('arabic_slug', $product->variations->where('langue', 'ar')->first()->slug ?? '') }}" required>
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
                                                             title="Synchroniser vers l'anglais">
                                                         <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i>
                                                     </button>
                                                     <button type="button" class="btn btn-outline-info preview-content"
                                                             data-field="#arabic_short_description"
                                                            data-title="Preview - Short description (Arabic)"
                                                             title="Preview">
                                                         <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                             <textarea name="arabic_short_description" id="arabic_short_description" class="ckeditor-field" required>{{ old('arabic_short_description', $product->variations->where('langue', 'ar')->first()->short_description ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="clipboard" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Exams description (Arabic) *
                                             </label>
                                             <textarea name="arabic_description_exams" id="arabic_description_exams" class="ckeditor-field" required>{{ old('arabic_description_exams', $product->variations->where('langue', 'ar')->first()->description_the_exams ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="help-circle" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Quizzes description (Arabic) *
                                             </label>
                                             <textarea name="arabic_description_quizzes" id="arabic_description_quizzes" class="ckeditor-field" required>{{ old('arabic_description_quizzes', $product->variations->where('langue', 'ar')->first()->description_the_quizzes ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Final exam description (Arabic)
                                             </label>
                                             <textarea name="arabic_description_final_exam" id="arabic_description_final_exam" class="ckeditor-field">{{ old('arabic_description_final_exam', $product->variations->where('langue', 'ar')->first()->description_final_exam ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Video exam description (Arabic)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="arabic_description_video_exam" id="arabic_description_video_exam" class="ckeditor-field">{{ old('arabic_description_video_exam', $product->variations->where('langue', 'ar')->first()->description_video_exam ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="briefcase" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Internship description (Arabic)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="arabic_description_stage" id="arabic_description_stage" class="ckeditor-field">{{ old('arabic_description_stage', $product->variations->where('langue', 'ar')->first()->description_stage ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="book-open" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Case study description (Arabic)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="arabic_description_study_case" id="arabic_description_study_case" class="ckeditor-field">{{ old('arabic_description_study_case', $product->variations->where('langue', 'ar')->first()->description_study_case ?? '') }}</textarea>
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
                                             <h6 class="mb-0">Section Anglaise</h6>
                                             <span class="badge bg-light text-dark ms-auto">English</span>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Nom (Anglais) *
                                             </label>
                                             <input type="text" name="english_name" class="form-control" value="{{ old('english_name', $product->variations->where('langue', 'en')->first()->name ?? '') }}" required>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="link" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Slug (Anglais) *
                                             </label>
                                             <input type="text" name="english_slug" class="form-control" value="{{ old('english_slug', $product->variations->where('langue', 'en')->first()->slug ?? '') }}" required>
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
                                                             title="Synchroniser vers l'arabe">
                                                         <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                                                     </button>
                                                     <button type="button" class="btn btn-outline-info preview-content"
                                                             data-field="#english_short_description"
                                                            data-title="Preview - Short description (English)"
                                                             title="Preview">
                                                         <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                             <textarea name="english_short_description" id="english_short_description" class="ckeditor-field" required>{{ old('english_short_description', $product->variations->where('langue', 'en')->first()->short_description ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="clipboard" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Exams description (English) *
                                             </label>
                                             <textarea name="english_description_exams" id="english_description_exams" class="ckeditor-field" required>{{ old('english_description_exams', $product->variations->where('langue', 'en')->first()->description_the_exams ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="help-circle" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Quizzes description (English) *
                                             </label>
                                             <textarea name="english_description_quizzes" id="english_description_quizzes" class="ckeditor-field" required>{{ old('english_description_quizzes', $product->variations->where('langue', 'en')->first()->description_the_quizzes ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Final exam description (English)
                                             </label>
                                             <textarea name="english_description_final_exam" id="english_description_final_exam" class="ckeditor-field">{{ old('english_description_final_exam', $product->variations->where('langue', 'en')->first()->description_final_exam ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Video exam description (English)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="english_description_video_exam" id="english_description_video_exam" class="ckeditor-field">{{ old('english_description_video_exam', $product->variations->where('langue', 'en')->first()->description_video_exam ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="briefcase" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Internship description (English)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="english_description_stage" id="english_description_stage" class="ckeditor-field">{{ old('english_description_stage', $product->variations->where('langue', 'en')->first()->description_stage ?? '') }}</textarea>
                                         </div>
                                         <div class="mb-3">
                                             <label class="form-label">
                                                 <i data-feather="book-open" class="me-1" style="width: 14px; height: 14px;"></i>
                                                 Case study description (English)
                                                 <span class="text-muted">(Optionnel)</span>
                                             </label>
                                             <textarea name="english_description_study_case" id="english_description_study_case" class="ckeditor-field">{{ old('english_description_study_case', $product->variations->where('langue', 'en')->first()->description_study_case ?? '') }}</textarea>
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
                                    <h5 class="mb-0">Product Data</h5>
                                 </div>
                             </div>

                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="hash" class="me-1" style="width: 14px; height: 14px;"></i>
                                         ID du produit
                                        <span class="text-muted">(Auto-generated)</span>
                                     </label>
                                   <input type="text" class="form-control" value="Auto-generated" disabled>
                                 </div>
                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="tag" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Category *
                                     </label>
                                     <select name="categories_id" class="form-select" required>
                                        <option value="">Select a category</option>
                                         @foreach($categories as $category)
                                             <option value="{{ $category->id }}" {{ old('categories_id', $product->categories_id) == $category->id ? 'selected' : '' }}>
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
                                             <option value="{{ $teacher->id }}" {{ old('teacher_id', $product->teacher_id) == $teacher->id ? 'selected' : '' }}>
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
                                             <option value="{{ $country->id }}" {{ old('country_id', $product->country_id) == $country->id ? 'selected' : '' }}>
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
                                             <option value="{{ $certif->id }}" {{ old('certif_id', $product->certif_id) == $certif->id ? 'selected' : '' }}>
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
                                         <option value="manual" {{ old('certificate_generation_mode', $product->certificate_generation_mode) == 'manual' ? 'selected' : '' }}>
                                             Manual - Admin will be notified to generate certificate
                                         </option>
                                         <option value="automatic" {{ old('certificate_generation_mode', $product->certificate_generation_mode) == 'automatic' ? 'selected' : '' }}>
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
                                     <input type="text" name="period" class="form-control" value="{{ old('period', $product->period) }}" placeholder="Ex: 3 mois, 6 semaines" required>
                                 </div>
                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                         Points *
                                     </label>
                                     <input type="number" name="point" class="form-control" value="{{ old('point', $product->point) }}" min="0" required>
                                 </div>
                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="dollar-sign" class="me-1" style="width: 14px; height: 14px;"></i>
                                         Prix
                                         <span class="text-muted">(Optionnel)</span>
                                     </label>
                                     <input type="number" step="0.01" name="prix" class="form-control" value="{{ old('prix', $product->prix) }}" min="0" placeholder="0.00">
                                 </div>

                                 <!-- Course Visibility Toggle -->
                                 <div class="mb-3">
                                     <div class="form-check form-switch">
                                         <input class="form-check-input" type="checkbox" role="switch" id="is_listed" name="is_listed" value="1" {{ old('is_listed', $product->is_listed ?? true) ? 'checked' : '' }}>
                                         <label class="form-check-label" for="is_listed">
                                             <i data-feather="eye" class="me-1" style="width: 14px; height: 14px;"></i>
                                             List course in catalog
                                         </label>
                                     </div>
                                     <small class="text-muted">When unchecked, course won't appear in user app catalog but enrolled students can still access it from their dashboard</small>
                                 </div>

                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                         Course Validity (Months)
                                         <span class="text-muted">(Empty = Lifetime Access)</span>
                                     </label>
                                     <input type="number" name="validity_months" class="form-control" value="{{ old('validity_months', $product->validity_months) }}" min="1" placeholder="Leave empty for lifetime access">
                                     <small class="text-muted">Set how many months students have access after purchase. Leave empty for lifetime access.</small>
                                 </div>
                                 <div class="mb-3">
                                     <label class="form-label">
                                         <i data-feather="repeat" class="me-1" style="width: 14px; height: 14px;"></i>
                                         Max Exam Attempts
                                         <span class="text-muted">(Default: 3)</span>
                                     </label>
                                     <input type="number" name="max_exam_attempts" class="form-control" value="{{ old('max_exam_attempts', $product->max_exam_attempts ?? 3) }}" min="1" max="100" placeholder="3">
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
                                     <input type="number" step="0.01" name="renewal_price" class="form-control" value="{{ old('renewal_price', $product->renewal_price ?? 50.00) }}" min="0" placeholder="50.00">
                                     <div class="form-text">
                                         <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                         Price charged for exam renewal after exceeding attempts
                                     </div>
                                 </div>

                                 <!-- Installment Payment Option -->
                                 <div class="mb-3">
                                     <div class="form-check form-switch">
                                         <input class="form-check-input" type="checkbox" role="switch" id="installment_allowed" name="installment_allowed" value="1" {{ old('installment_allowed', $product->installment_allowed ?? false) ? 'checked' : '' }}>
                                         <label class="form-check-label" for="installment_allowed">
                                             <i data-feather="credit-card" class="me-1" style="width: 14px; height: 14px;"></i>
                                             Allow Installment Payments
                                         </label>
                                     </div>
                                     <small class="text-muted">When enabled, students can pay for this course in monthly installments. The number of installments equals the "Course Validity (Months)" value above. Each installment = Total Price ÷ Validity Months.</small>
                                 </div>

                                 <div class="mb-3">
                                     <div class="form-check">
                                         <input class="form-check-input" type="checkbox" name="iscach" id="iscach" value="1" {{ old('iscach', $product->iscach) ? 'checked' : '' }}>
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
                                        Image principale
                                        <span class="text-muted">(Optional - keeps current if not changed)</span>
                                    </label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <div class="invalid-feedback">
                                        L'image principale est obligatoire.
                                    </div>
                                    <div class="form-text">
                                        <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Accepted formats: JPEG, PNG, JPG, GIF, WebP (max 50MB)
                                    </div>
                                    @if($product->image)
                                        <div class="mt-3" id="currentImageCard">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">
                                                        <i data-feather="image" class="me-2"></i>
                                                        Image actuelle
                                                    </h6>
                                                </div>
                                                <div class="card-body text-center">
                                                    <img src="{{ asset('uploads/products/images/' . $product->image) }}" alt="Image actuelle" class="img-fluid rounded" style="max-height: 200px;">
                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ $product->image }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i data-feather="eye" class="me-2"></i>
                                                    New image preview
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearImageSelection()">
                                                    <i data-feather="x" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    Annuler
                                                </button>
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

                             <!-- Dates et Informations -->
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
                                                     <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', $product->date_debut) }}">
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="mb-3">
                                                     <label class="form-label">
                                                         <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                                         Date de fin
                                                     </label>
                                                     <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin', $product->date_fin) }}">
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
                                                        <option value="fa" {{ old('type_course', $product->type_course) == 'fa' ? 'selected' : '' }}>Foundation English (FA)</option>
                                                        <option value="fi" {{ old('type_course', $product->type_course) == 'fi' ? 'selected' : '' }}>Fluency &amp; Interaction (FI)</option>
                                                        <option value="pt" {{ old('type_course', $product->type_course) == 'pt' ? 'selected' : '' }}>Proficiency Track (PT)</option>
                                                        <option value="autres" {{ old('type_course', $product->type_course) == 'autres' ? 'selected' : '' }}>Autres</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="goverrnement" id="goverrnement" value="1" {{ old('goverrnement', $product->goverrnement) ? 'checked' : '' }}>
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
                                                     <input class="form-check-input" type="checkbox" id="is_classroom" name="is_classroom" value="1" {{ old('is_classroom', $product->is_classroom) ? 'checked' : '' }}>
                                                     <label class="form-check-label" for="is_classroom">
                                                        <strong>On-site Training</strong>
                                                         <small class="d-block text-muted">Cours en salle de classe</small>
                                                     </label>
                                                 </div>
                                             </div>
                                             <div class="col-md-4">
                                                 <div class="form-check form-switch">
                                                     <input class="form-check-input" type="checkbox" id="is_zoom" name="is_zoom" value="1" {{ old('is_zoom', $product->is_zoom) ? 'checked' : '' }}>
                                                     <label class="form-check-label" for="is_zoom">
                                                        <strong>Zoom Training</strong>
                                                         <small class="d-block text-muted">Course via videoconference</small>
                                                     </label>
                                                 </div>
                                             </div>
                                             <div class="col-md-4">
                                                 <div class="form-check form-switch">
                                                     <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" {{ old('is_online', $product->is_online) ? 'checked' : '' }}>
                                                     <label class="form-check-label" for="is_online">
                                                        <strong>Online Training</strong>
                                                         <small class="d-block text-muted">Cours en ligne asynchrone</small>
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


                     <!-- Quiz et Examens Section -->
                     <div class="tab-pane fade" id="quizzes" role="tabpanel">
                         <div class="row mt-3">
                             <div class="col-12">
                                 <div class="d-flex align-items-center mb-3">
                                     <i data-feather="help-circle" class="text-info me-2"></i>
                                     <h5 class="mb-0">Quiz et Examens</h5>
                                     <span class="badge bg-info ms-2">Gestion dynamique</span>
                                 </div>
                                @if($product->installment_allowed)
                                    <div class="alert alert-info py-2">
                                        <i data-feather="calendar" class="me-1"></i>
                                        Installments are enabled. Please select the unlock month for each quiz/exam.
                                    </div>
                                @endif
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
                                                    <input class="form-check-input" type="checkbox" id="is_stage" name="is_stage" value="1" {{ old('is_stage', $product->is_stage) ? 'checked' : '' }}>
                                                     <label class="form-check-label" for="is_stage">
                                                         <strong>Stage</strong>
                                                         <small class="d-block text-muted">Ce cours inclut un stage pratique</small>
                                                     </label>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_exam_video" name="is_exam_video" value="1" {{ old('is_exam_video', $product->is_exam_video) ? 'checked' : '' }}>
                                                     <label class="form-check-label" for="is_exam_video">
                                                        <strong>Video exam</strong>
                                                        <small class="d-block text-muted">This course requires a video exam</small>
                                                     </label>
                                                 </div>
                                             </div>
                                         </div>
                                         <div class="row mt-3">
                                             <div class="col-md-6">
                                                 <label class="form-label">
                                                     <strong>Practical Exam Type</strong>
                                                     <small class="d-block text-muted">Select the type of practical exam</small>
                                                 </label>
                                                 <select class="form-select" id="practical_exam_type" name="practical_exam_type">
                                                     <option value="">-- No Practical Exam --</option>
                                                     <option value="online" {{ old('practical_exam_type', $product->practical_exam_type) === 'online' ? 'selected' : '' }}>🌐 Online (Video Submission)</option>
                                                     <option value="classroom" {{ old('practical_exam_type', $product->practical_exam_type) === 'classroom' ? 'selected' : '' }}>🏫 Classroom (Physical Exam)</option>
                                                 </select>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Stage Documents Section -->
                                 <div class="card border-primary mt-3" id="stageDocumentsSection" style="display: {{ $product->is_stage ? 'block' : 'none' }};">
                                     <div class="card-header bg-light">
                                         <div class="d-flex justify-content-between align-items-center">
                                             <div class="d-flex align-items-center">
                                                 <i data-feather="file-text" class="me-2 text-primary"></i>
                                                 <h6 class="mb-0">Documents de Stage</h6>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <p class="text-muted small mb-3">Téléchargez les deux documents requis pour le stage:</p>

                                         <!-- Request Letter -->
                                         <div class="mb-4">
                                             <label class="form-label"><strong>خطاب الطلب (Official Request Letter)</strong></label>
                                             <div class="mb-2">
                                                 @php
                                                     $requestLetter = $product->stageDocuments()->where('document_type', 'request_letter')->first();
                                                 @endphp
                                                 @if($requestLetter)
                                                     <div class="alert alert-success d-flex justify-content-between align-items-center mb-2">
                                                         <div>
                                                             <i class="bi bi-file-pdf me-2"></i>
                                                             <strong>{{ $requestLetter->file_name }}</strong>
                                                             <br>
                                                             <small class="text-muted">{{ $requestLetter->formatted_size }} • {{ $requestLetter->created_at->format('d/m/Y H:i') }}</small>
                                                         </div>
                                                         <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument('{{ $requestLetter->id }}')">
                                                             <i data-feather="trash-2"></i>
                                                         </button>
                                                     </div>
                                                 @endif
                                             </div>
                                             <input type="file" class="form-control" id="request_letter" name="request_letter" accept=".pdf,.doc,.docx" onchange="uploadDocument(this, 'request_letter')">
                                             <small class="d-block text-muted mt-1">PDF, DOC, DOCX (Max 10MB)</small>
                                         </div>

                                         <!-- Evaluation Form -->
                                         <div class="mb-4">
                                             <label class="form-label"><strong>استمارة التقييم (Internship Evaluation Form)</strong></label>
                                             <div class="mb-2">
                                                 @php
                                                     $evaluationForm = $product->stageDocuments()->where('document_type', 'evaluation_form')->first();
                                                 @endphp
                                                 @if($evaluationForm)
                                                     <div class="alert alert-success d-flex justify-content-between align-items-center mb-2">
                                                         <div>
                                                             <i class="bi bi-file-pdf me-2"></i>
                                                             <strong>{{ $evaluationForm->file_name }}</strong>
                                                             <br>
                                                             <small class="text-muted">{{ $evaluationForm->formatted_size }} • {{ $evaluationForm->created_at->format('d/m/Y H:i') }}</small>
                                                         </div>
                                                         <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument('{{ $evaluationForm->id }}')">
                                                             <i data-feather="trash-2"></i>
                                                         </button>
                                                     </div>
                                                 @endif
                                             </div>
                                             <input type="file" class="form-control" id="evaluation_form" name="evaluation_form" accept=".pdf,.doc,.docx" onchange="uploadDocument(this, 'evaluation_form')">
                                             <small class="d-block text-muted mt-1">PDF, DOC, DOCX (Max 10MB)</small>
                                         </div>
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
                                                 Ajouter un quiz
                                             </button>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <div id="quizContainer">
                                             <!-- Les lignes de quiz seront ajoutées ici dynamiquement -->
                                             <div class="text-center py-4" id="noQuizMessage">
                                                 <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">No quiz selected</p>
                                                 <button type="button" class="btn btn-sm btn-outline-success" onclick="addQuizRow()">
                                                     <i data-feather="plus" class="me-1"></i>
                                                     Ajouter le premier quiz
                                                 </button>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- Section Examens -->
                             <div class="col-md-6">
                                 <div class="card border-warning">
                                     <div class="card-header">
                                         <div class="d-flex justify-content-between align-items-center">
                                             <div class="d-flex align-items-center">
                                                 <i data-feather="file-text" class="me-2"></i>
                                                 <h6 class="mb-0">Examens</h6>
                                                <span class="badge bg-light text-dark ms-2">Assessments</span>
                                             </div>
                                             <button type="button" class="btn btn-sm btn-warning" id="addExamBtn">
                                                 <i data-feather="plus" class="me-1"></i>
                                                 Ajouter un examen
                                             </button>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <div id="examContainer">
                                             <!-- Les lignes d'examens seront ajoutées ici dynamiquement -->
                                             <div class="text-center py-4" id="noExamMessage">
                                                 <i data-feather="file-text" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">No exam selected</p>
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
                                               <span class="badge bg-light text-dark ms-2">Learning materials</span>
                                               <span class="badge bg-info text-white ms-2"><i data-feather="move" style="width: 12px; height: 12px;"></i> Drag to reorder</span>
                                             </div>
                                             <button type="button" class="btn btn-sm btn-info" id="addStudyBtn">
                                                 <i data-feather="plus" class="me-1"></i>
                                                 Ajouter une ressource
                                             </button>
                                         </div>
                                     </div>
                                     <div class="card-body">
                                         <!-- Column headers -->
                                         <div class="row align-items-center mb-2 px-3 text-muted small fw-bold" id="studyColumnHeaders" style="display: none;">
                                             <div class="col-md-1"></div>
                                             <div class="col-md-3">Resource</div>
                                             <div class="col-md-2"><i data-feather="calendar" style="width: 14px; height: 14px;"></i> Drip Month</div>
                                             <div class="col-md-2">Days after access</div>
                                             <div class="col-md-2">File</div>
                                             <div class="col-md-2 text-end">Actions</div>
                                         </div>
                                         <div id="studyContainer" class="sortable-study-container">
                                             <!-- Les lignes de ressources d'étude seront ajoutées ici dynamiquement -->
                                             <div class="text-center py-4" id="noStudyMessage">
                                                 <i data-feather="book-open" class="text-muted" style="width: 48px; height: 48px;"></i>
                                                <p class="text-muted mt-2">No study resource selected</p>
                                                 <button type="button" class="btn btn-sm btn-outline-info" onclick="addStudyRow()">
                                                     <i data-feather="plus" class="me-1"></i>
                                                    Add the first resource
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
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('submitFormBtn').click()">
                            <i data-feather="save" class="me-2"></i>
                        Update Product
                        </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    // Must be global: addExistingQuizRow / addExistingExamRow / loadExistingData are defined outside DOMContentLoaded
    // and template literals resolve these names at runtime from the global object.
    window.isInstallmentEnabled = {{ $product->installment_allowed ? 'true' : 'false' }};
    window.getInstallmentMonthOptions = function (selectedValue) {
        if (selectedValue === undefined || selectedValue === null) {
            selectedValue = '';
        }
        var validityInput = document.querySelector('input[name="validity_months"]');
        var maxMonths = validityInput && validityInput.value ? parseInt(validityInput.value, 10) : NaN;
        if (!maxMonths || maxMonths < 1) {
            maxMonths = 1;
        }
        var options = '<option value="">Select installment month</option>';
        for (var i = 1; i <= maxMonths; i++) {
            options += '<option value="' + i + '"' + (String(selectedValue) === String(i) ? ' selected' : '') + '>Month ' + i + '</option>';
        }
        return options;
    };
})();

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

    // Initialize Arabic editors
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
                        // Update progress after change
                        setTimeout(updateTabProgress, 100);
                    });

                    console.log(`✅ CKEditor initialisé pour: ${editorId}`);
                })
                .catch(error => {
                    console.error(`❌ Erreur CKEditor pour ${editorId}:`, error);
                    // Mark as unavailable to avoid errors
                    ckeditorInstances[editorId] = null;
                });
        } else {
            console.warn(`⚠️ CKEditor element not found: ${editorId}`);
        }
    });

    // Initialize English editors
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
                        // Update progress after change
                        setTimeout(updateTabProgress, 100);
                    });

                    console.log(`✅ CKEditor initialisé pour: ${editorId}`);
                })
                .catch(error => {
                    console.error(`❌ Erreur CKEditor pour ${editorId}:`, error);
                    // Mark as unavailable to avoid errors
                    ckeditorInstances[editorId] = null;
                });
        } else {
            console.warn(`⚠️ CKEditor element not found: ${editorId}`);
        }
    });

    // Function to verify all CKEditor instances are initialized
    function waitForCKEditorsToLoad() {
        return new Promise((resolve) => {
            const checkInterval = setInterval(() => {
                const allEditorsLoaded = arabicEditors.concat(englishEditors).every(editorId => {
                    return ckeditorInstances[editorId] !== undefined;
                });

                if (allEditorsLoaded) {
                    clearInterval(checkInterval);
                    console.log('✅ All CKEditor instances loaded');
                    resolve();
                }
            }, 100);

            // Timeout after 10 seconds
            setTimeout(() => {
                clearInterval(checkInterval);
                console.warn('⚠️ Timeout: Some CKEditor instances are not loaded');
                resolve();
            }, 10000);
        });
    }

    // Wait for all editors to load before initializing the rest
    waitForCKEditorsToLoad().then(() => {
        // Initial progress update
        updateTabProgress();
    });

    // Navigation des onglets avec indicateur de progression
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Reset editors on tab change
            setTimeout(() => {
                // CKEditor editors update automatically
            }, 100);

            // Update progress indicator
            updateTabProgress();
        });
    });

    // Function to update tabs progress indicator
    function updateTabProgress() {
        const tabs = ['public', 'data', 'types', 'quizzes', 'study'];
        const completedTabs = [];

        tabs.forEach(tabId => {
            const tabContent = document.getElementById(tabId);
            if (tabContent) {
                const requiredFields = tabContent.querySelectorAll('[required]');
                let completedFields = 0;

                requiredFields.forEach(field => {
                    if (field.type === 'file') {
                        // For file inputs, check if files are selected
                        if (field.files && field.files.length > 0) {
                            completedFields++;
                        }
                    } else if (field.value.trim() !== '') {
                        completedFields++;
                    }
                });

                // Also check CKEditor fields
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

                // Update tab icon
                const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                if (tabButton) {
                    const icon = tabButton.querySelector('i');
                    if (icon) {
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

                        // Reset Feather Icons
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                }
            }
        });

        // Update submit button
        const submitBtn = document.getElementById('submitFormBtn');
        if (submitBtn) {
            if (completedTabs.length === tabs.length) {
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-success');
                submitBtn.innerHTML = '<i data-feather="check-circle" class="me-2"></i>Ready to update product';
            } else {
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
                submitBtn.innerHTML = '<i data-feather="save" class="me-2"></i>Update Product';
            }

            // Reset Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    }

    // Validation du formulaire
    document.getElementById('productForm').addEventListener('submit', function(e) {
        // Update study order before submit
        if (typeof updateStudyOrder === 'function') {
            updateStudyOrder();
        }

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
        // Synchronization between CKEditor editors
                const content = ckeditorInstances[sourceId].getData();
                ckeditorInstances[targetId].setData(content);
                console.log(`✅ Content synchronized from ${sourceId} to ${targetId}`);
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
            showNotification('Content synchronized successfully!', 'success');
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

    // Realtime search events
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

    // Realtime preview
    const previewButtons = document.querySelectorAll('.preview-content');
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fieldId = this.getAttribute('data-field');
            const fieldIdClean = fieldId.replace('#', '');

            if (ckeditorInstances[fieldIdClean]) {
                const content = ckeditorInstances[fieldIdClean].getData();
                showPreview(content, this.getAttribute('data-title') || 'Preview');
            } else {
                const editor = document.querySelector(fieldId);
                if (editor) {
                    showPreview(editor.value, this.getAttribute('data-title') || 'Preview');
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

    // Available quizzes and exams data (to be fetched from server)
    const availableQuizzes = @json($quizzes->filter(function($quiz) { return stripos($quiz->type->titre, 'quiz') !== false; })->values());
    const availableExams = @json($quizzes->filter(function($quiz) { return stripos($quiz->type->titre, 'exam') !== false; })->values());
    const availableTrainingCases = @json($trainingCases);
    var isInstallmentEnabled = window.isInstallmentEnabled;
    var getInstallmentMonthOptions = window.getInstallmentMonthOptions;

    // Debug: Check received data
    console.log('🔍 availableQuizzes:', availableQuizzes);
    console.log('🔍 availableExams:', availableExams);
    console.log('🔍 availableTrainingCases:', availableTrainingCases);
    console.log('🔍 Type de availableQuizzes:', typeof availableQuizzes);
    console.log('🔍 Type de availableExams:', typeof availableExams);
    console.log('🔍 availableQuizzes est un tableau?', Array.isArray(availableQuizzes));
    console.log('🔍 availableExams est un tableau?', Array.isArray(availableExams));
    console.log('🔍 availableTrainingCases est un tableau?', Array.isArray(availableTrainingCases));

    // Fonction pour ajouter une ligne de quiz
    window.addQuizRow = function() {
        const container = document.getElementById('quizContainer');
        const noQuizMessage = document.getElementById('noQuizMessage');

        // Ensure availableQuizzes is an array
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
                <div class="col-md-3">
                    <label class="form-label">Select a Quiz</label>
                    <select name="quiz_ids[]" class="form-select quiz-select" required>
                        <option value="">Choisir un quiz...</option>
                        ${availableQuizzes.map(quiz => `
                            <option value="${quiz.id}" data-name-ar="${quiz.name_ar}" data-name-en="${quiz.name_en}" data-score="${quiz.score}" data-type="${quiz.type?.titre || ''}">
                                ${quiz.name_ar} - ${quiz.name_en} (${quiz.score}/100)
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nombre de questions</label>
                    <input type="number" name="quiz_nb_questions[]" class="form-control quiz-nb-question" value="10" min="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Score</label>
                    <input type="number" name="quiz_scores[]" class="form-control quiz-score" value="50" min="0" max="100">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <select name="quiz_installment_months[]" class="form-select form-select-sm" required>
                        ${getInstallmentMonthOptions()}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Days after access</label>
                    <input type="number" name="quiz_opens_after_purchase_days[]" class="form-control form-control-sm" min="1" max="3650" placeholder="Optional">
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

            // Reset Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Add remove event
        newRow.querySelector('.remove-quiz').addEventListener('click', function() {
                    newRow.remove();
            checkQuizContainer();
        });

        // Add change event
        newRow.querySelector('.quiz-select').addEventListener('change', function() {
            updateQuizDetails(newRow);
            preventDuplicateSelection(this);
            reactivateOptions(this);
        });

        // Reset Select2 to include the new select
        setTimeout(() => {
            reinitializeAllSelect2();
        }, 100);

        console.log('✅ Quiz row added');
    };

    // Fonction pour ajouter une ligne d'examen
    window.addExamRow = function() {
        const container = document.getElementById('examContainer');
        const noExamMessage = document.getElementById('noExamMessage');

        // Ensure availableExams is an array
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
        newRow.className = 'exam-row border rounded p-3 mb-3 bg-light shadow-sm';
        newRow.innerHTML = `
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">
                        Exam Type <span class="text-danger">*</span>
                    </label>
                    <select name="exam_types[]" class="form-select form-select-sm exam-type-select" required>
                        <option value="">Choose type...</option>
                        <option value="theoretical">📝 Theoretical (Quiz)</option>
                        <option value="practical">🎯 Practical (Training Case)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">
                        Select Exam/Training Case
                    </label>
                    <select name="exam_ids[]" class="form-select form-select-sm exam-select" required disabled>
                        <option value="">First select exam type...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">
                        Month
                    </label>
                    <select name="exam_installment_months[]" class="form-select form-select-sm exam-installment-month" required>
                        ${getInstallmentMonthOptions()}
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">
                        Number of Questions
                    </label>
                    <input type="number" name="exam_nb_questions[]" class="form-control form-control-sm exam-nb-question" value="10" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">
                        Score (Max: 100)
                    </label>
                    <input type="number" name="exam_scores[]" class="form-control form-control-sm exam-score" value="50" min="0" max="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">
                        Days after access
                    </label>
                    <input type="number" name="exam_opens_after_purchase_days[]" class="form-control form-control-sm exam-opens-after-days" value="" min="1" max="3650" placeholder="Optional">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-exam w-100" title="Remove this exam">
                        <i data-feather="trash-2" class="me-1" style="width: 14px; height: 14px;"></i>
                        Remove Exam
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" class="exam-use-own-hidden" name="exam_use_own_questions_temp_hidden_${examIndex}" value="0">
                        <input class="form-check-input exam-use-own-checkbox" type="checkbox" name="exam_use_own_questions_temp_${examIndex}" value="1" id="exam_use_own_${examIndex}" data-exam-index="${examIndex}">
                        <label class="form-check-label" for="exam_use_own_${examIndex}">
                            Use Exam's Own Questions (if unchecked, will fetch from course quizzes)
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mt-2" style="display:none;">
                <div class="col-12">
                    <div class="alert alert-info mb-0 py-2 exam-details-container">
                        <small class="exam-details"></small>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(newRow);
        examIndex++;

        // Reset Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Add remove event
        newRow.querySelector('.remove-exam').addEventListener('click', function() {
            newRow.remove();
            checkExamContainer();
        });

        // Add exam type change event
        newRow.querySelector('.exam-type-select').addEventListener('change', function() {
            const examSelect = newRow.querySelector('.exam-select');
            const examType = this.value;
            const installmentMonthSelect = newRow.querySelector('.exam-installment-month');
            const opensAfterDaysInput = newRow.querySelector('.exam-opens-after-days');

            // Destroy Select2 if it exists
            if ($(examSelect).hasClass('select2-hidden-accessible')) {
                $(examSelect).select2('destroy');
            }

            // Clear and disable exam select
            examSelect.innerHTML = '<option value="">Loading...</option>';
            examSelect.disabled = true;

            if (examType === 'theoretical') {
                if (installmentMonthSelect) {
                    installmentMonthSelect.setAttribute('name', 'exam_installment_months[]');
                    installmentMonthSelect.disabled = false;
                    installmentMonthSelect.required = isInstallmentEnabled;
                    const instCol = installmentMonthSelect.closest('.col-md-3');
                    instCol?.querySelectorAll('.exam-installment-month-hidden-fallback').forEach((el) => el.remove());
                }
                if (opensAfterDaysInput) {
                    opensAfterDaysInput.readOnly = false;
                    opensAfterDaysInput.classList.remove('bg-light', 'text-muted');
                }
                // Load quizzes
                examSelect.innerHTML = `
                    <option value="">Choose quiz...</option>
                    ${availableExams.map(exam => `
                        <option value="${exam.id}" data-name-ar="${exam.name_ar}" data-name-en="${exam.name_en}" data-score="${exam.score}">
                            ${exam.name_ar} - ${exam.name_en} (${exam.score}/100)
                        </option>
                    `).join('')}
                `;
                examSelect.disabled = false;
            } else if (examType === 'practical') {
                if (installmentMonthSelect) {
                    installmentMonthSelect.disabled = true;
                    installmentMonthSelect.required = false;
                    installmentMonthSelect.value = '';
                    // Disabled selects are not submitted — keep array aligned with exam_ids[] / exam_opens_after_purchase_days[]
                    installmentMonthSelect.removeAttribute('name');
                    const instCol = installmentMonthSelect.closest('.col-md-3');
                    if (instCol && !instCol.querySelector('.exam-installment-month-hidden-fallback')) {
                        const hid = document.createElement('input');
                        hid.type = 'hidden';
                        hid.className = 'exam-installment-month-hidden-fallback';
                        hid.name = 'exam_installment_months[]';
                        hid.value = '';
                        instCol.appendChild(hid);
                    }
                }
                if (opensAfterDaysInput) {
                    opensAfterDaysInput.readOnly = true;
                    opensAfterDaysInput.value = '';
                    opensAfterDaysInput.classList.add('bg-light', 'text-muted');
                }
                // Load training cases
                examSelect.innerHTML = `
                    <option value="">Choose training case...</option>
                    ${availableTrainingCases.map(tc => `
                        <option value="tc_${tc.id}" data-name-ar="${tc.name}" data-name-en="${tc.name}" data-files="${tc.files_count || 0}">
                            ${tc.name} (${tc.files_count || 0} file(s))
                        </option>
                    `).join('')}
                `;
                examSelect.disabled = false;
            }

            // Reinitialize Select2 for this specific select
            setTimeout(() => {
                $(examSelect).select2({
                    width: '100%',
                    placeholder: 'Select an option'
                });
            }, 100);
        });

        // Add change event
        newRow.querySelector('.exam-select').addEventListener('change', function() {
            // Update checkbox name with actual exam ID
            const checkbox = newRow.querySelector('.exam-use-own-checkbox');
            const hiddenInput = newRow.querySelector('.exam-use-own-hidden');
            if (checkbox && this.value) {
                checkbox.setAttribute('name', `exam_use_own_questions[${this.value}]`);
            }
            if (hiddenInput && this.value) {
                hiddenInput.setAttribute('name', `exam_use_own_questions[${this.value}]`);
            }
            updateExamDetails(newRow);
            preventDuplicateSelection(this);
            reactivateOptions(this);
        });

        // Réinitialiser Select2 pour inclure le nouveau select d'examen
        setTimeout(() => {
            reinitializeAllSelect2();
        }, 100);

        console.log('✅ Exam row added');
    };

    // Function to update quiz details
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

    // Function to update exam details
    function updateExamDetails(row) {
        const select = row.querySelector('.exam-select');
        const scoreInput = row.querySelector('.exam-score');
        const detailsDiv = row.querySelector('.exam-details');
        const detailsContainer = row.closest('.exam-row').querySelector('.row.mt-2');

        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const nameAr = selectedOption.getAttribute('data-name-ar');
            const nameEn = selectedOption.getAttribute('data-name-en');
            const score = selectedOption.getAttribute('data-score');
            const files = selectedOption.getAttribute('data-files');

            if (scoreInput && scoreInput.dataset.userEdited !== 'true') {
                scoreInput.value = 50;
            }

            if (files) {
                detailsDiv.innerHTML = `📁 <strong>${nameAr}</strong> - ${files} file(s)`;
            } else {
                detailsDiv.innerHTML = `📚 <strong>${nameAr}</strong> - ${nameEn}`;
            }
            detailsContainer.style.display = 'block';
        } else {
            if (scoreInput) {
                if (scoreInput.dataset) {
                    scoreInput.dataset.userEdited = 'false';
                }
                scoreInput.value = 50;
            }
            detailsDiv.innerHTML = '';
            detailsContainer.style.display = 'none';
        }
    }

    // Function to check if quiz container is empty
    function checkQuizContainer() {
        const container = document.getElementById('quizContainer');
        const noQuizMessage = document.getElementById('noQuizMessage');
        const quizRows = container.querySelectorAll('.quiz-row');

        if (quizRows.length === 0 && noQuizMessage) {
            noQuizMessage.style.display = 'block';
        }
    }

    // Function to check if exams container is empty
    function checkExamContainer() {
        const container = document.getElementById('examContainer');
        const noExamMessage = document.getElementById('noExamMessage');
        const examRows = container.querySelectorAll('.exam-row');

        if (examRows.length === 0 && noExamMessage) {
            noExamMessage.style.display = 'block';
        }
    }

    // Events for add buttons
    const addQuizBtn = document.getElementById('addQuizBtn');
    const addExamBtn = document.getElementById('addExamBtn');

    if (addQuizBtn) {
        addQuizBtn.addEventListener('click', addQuizRow);
    }

    if (addExamBtn) {
        addExamBtn.addEventListener('click', addExamRow);
    }

    console.log('🎉 Quiz and exams management initialized');

    // ===== STUDY RESOURCES MANAGEMENT =====

    // Variables for study resources
    let studyIndex = 0;

    // Available resources data
    const availableResources = @json($resources);

    // Debug: Check received data
    console.log('🔍 availableResources:', availableResources);
    console.log('🔍 Type de availableResources:', typeof availableResources);
    console.log('🔍 availableResources est un tableau?', Array.isArray(availableResources));

    // Function to add a study resource row
    window.addStudyRow = function() {
        const container = document.getElementById('studyContainer');
        const noStudyMessage = document.getElementById('noStudyMessage');

        // Ensure availableResources is an array
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
        // Build month options for new rows
        const maxMonths = parseInt(document.querySelector('input[name="validity_months"]')?.value) || 12;
        let newMonthOptions = '<option value="0">-- No Month --</option>';
        for (let i = 1; i <= maxMonths; i++) {
            newMonthOptions += `<option value="${i}">Month ${i}</option>`;
        }

        newRow.className = 'study-row border rounded p-3 mb-3 new-study-row';
        newRow.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-1">
                    <div class="study-drag-handle text-center text-muted">
                        <i data-feather="menu" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Select a Resource</label>
                    <select class="form-select study-resource-select" style="width: 100%;">
                        <option value="">Choisir une ressource...</option>
                        ${availableResources.map(resource => `
                            <option value="${resource.id}" data-name-ar="${resource.name_ar}" data-name-en="${resource.name_en}" data-type="${resource.type}">
                                ${resource.name_ar} - ${resource.name_en} (${resource.type})
                            </option>
                        `).join('')}
                    </select>
                    <input type="hidden" name="study_items[]" value="" class="study-resource-id-input">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Drip Month</label>
                    <select class="form-select form-select-sm milestone-month-select-new">
                        ${newMonthOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-wrap">Days after access</label>
                    <input type="number" class="form-control form-control-sm study-opens-after-days" name="" min="1" max="3650" placeholder="Optional" title="Calendar days after course access starts">
                </div>
                <div class="col-md-1 text-end">
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

        // Reset Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Add remove event
        newRow.querySelector('.remove-study').addEventListener('click', function() {
            newRow.remove();
            checkStudyContainer();
        });

        // Add change event - update hidden input when resource is selected
        const select = newRow.querySelector('.study-resource-select');
        const hiddenInput = newRow.querySelector('.study-resource-id-input');
        const milestoneSelect = newRow.querySelector('.milestone-month-select-new');
        const opensInput = newRow.querySelector('.study-opens-after-days');
        select.addEventListener('change', function() {
            hiddenInput.value = this.value;
            newRow.setAttribute('data-resource-id', this.value);
            // Update the milestone select name to include the resource ID
            if (this.value) {
                milestoneSelect.name = `content_milestones[${this.value}]`;
                if (opensInput) {
                    opensInput.name = `content_opens_after_days[${this.value}]`;
                }
            } else {
                milestoneSelect.name = '';
                if (opensInput) {
                    opensInput.name = '';
                }
            }
            updateStudyDetails(newRow);
        });

        console.log('✅ Study resource row added');
        checkStudyContainer();
    };

    // Function to update a study resource details
    function updateStudyDetails(row) {
        const select = row.querySelector('.study-resource-select');
        const detailsDiv = row.querySelector('.study-details');

        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const nameAr = selectedOption.getAttribute('data-name-ar');
            const nameEn = selectedOption.getAttribute('data-name-en');
            const type = selectedOption.getAttribute('data-type');

            // Show details
            detailsDiv.innerHTML = `<strong>Nom (Arabe):</strong> ${nameAr} | <strong>Nom (Anglais):</strong> ${nameEn} | <strong>Type:</strong> ${type}`;
        } else {
            detailsDiv.innerHTML = '';
        }
    }

    // Function to check if study resources container is empty and toggle headers
    function checkStudyContainer() {
        const container = document.getElementById('studyContainer');
        const noStudyMessage = document.getElementById('noStudyMessage');
        const columnHeaders = document.getElementById('studyColumnHeaders');
        const studyRows = container.querySelectorAll('.study-row');

        if (studyRows.length === 0) {
            if (noStudyMessage) noStudyMessage.style.display = 'block';
            if (columnHeaders) columnHeaders.style.display = 'none';
        } else {
            if (noStudyMessage) noStudyMessage.style.display = 'none';
            if (columnHeaders) columnHeaders.style.display = 'flex';
        }
    }

    // Event for add study resource button
    const addStudyBtn = document.getElementById('addStudyBtn');

    if (addStudyBtn) {
        addStudyBtn.addEventListener('click', addStudyRow);
    }

    console.log('🎉 Study resources management initialized');

    // ===== VALIDATION SYSTEM WITH TOASTS =====

    // Function to create and show a toast
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

        // Reset Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Remove toast from DOM after it is hidden
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

        // Validation for file inputs
        if (field.type === 'file') {
            // If field is required and no file is selected
            if (isRequired && (!field.files || field.files.length === 0)) {
                field.classList.add('is-invalid');
                return { valid: false, message: `Le champ "${fieldName}" est obligatoire.` };
            }

            // If a file is selected, validate type and size
            if (field.files && field.files.length > 0) {
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
        }

        // Validation for emails
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
            { selector: 'select[name="categories_id"]', name: 'Category' },
            { selector: 'select[name="teacher_id"]', name: 'Teacher' },
            { selector: 'select[name="country_id"]', name: 'Country' },
            { selector: 'input[name="period"]', name: 'Period' },
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
            { id: 'arabic_short_description', name: 'Short description (Arabic)' },
            { id: 'arabic_description_exams', name: 'Description des examens (Arabe)' },
            { id: 'arabic_description_quizzes', name: 'Description des quiz (Arabe)' },
            { id: 'english_short_description', name: 'Short description (English)' },
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

                errorMessage += `\n⚠️ Le produit sera mis à jour avec le statut "Brouillon" (breuillant = true)`;

                Swal.fire({
                    icon: 'warning',
                    title: 'Erreurs de validation',
                    text: errorMessage,
                    showCancelButton: true,
                    confirmButtonText: 'Corriger',
                    cancelButtonText: 'Mettre à jour quand même',
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
                        // Mettre à jour quand même avec breuillant = true
                        submitFormAjax(true); // true = force save with breuillant
                    }
                });

                return;
            }

            // Si tout est valide, demander confirmation avec Sweet Alert
            Swal.fire({
                title: 'Confirmer la mise à jour',
                text: 'Êtes-vous sûr de vouloir mettre à jour ce produit ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Oui, mettre à jour le produit',
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
        // Afficher un loader avec message adapté
        const title = forceSave ? 'Mise à jour en cours...' : 'Mise à jour en cours...';
        const text = forceSave ? 'Mise à jour du produit en mode brouillon...' : 'Veuillez patienter pendant la mise à jour du produit';

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

        // Manually add all study items in their current DOM order
        const studyRows = document.querySelectorAll('#studyContainer .study-row');
        studyRows.forEach((row, index) => {
            const resourceIdInput = row.querySelector('.study-resource-id-input');
            if (resourceIdInput && resourceIdInput.value) {
                formData.append('study_items[]', resourceIdInput.value);
            }

            // Also manually add content milestone values
            const milestoneSelect = row.querySelector('.milestone-month-select, .milestone-month-select-new');
            const resourceId = row.getAttribute('data-resource-id') || (resourceIdInput ? resourceIdInput.value : null);
            if (milestoneSelect && resourceId) {
                formData.set(`content_milestones[${resourceId}]`, milestoneSelect.value);
            }
            const opensInput = row.querySelector('input[name^="content_opens_after_days"], .study-opens-after-days');
            if (opensInput && resourceId) {
                const oname = opensInput.getAttribute('name');
                if (oname) {
                    formData.set(oname, opensInput.value || '');
                } else {
                    formData.set(`content_opens_after_days[${resourceId}]`, opensInput.value || '');
                }
            }
        });

        // Manually add all study resource select values to ensure they're included
        const studySelects = document.querySelectorAll('.study-resource-select');
        studySelects.forEach((select, index) => {
            if (select.value && select.value !== '') {
                const fieldName = `study_resources[${index}][resource_id]`;
                formData.set(fieldName, select.value);
            }
        });

        // Ajouter le paramètre forceSave pour indiquer au serveur
        formData.append('force_save', forceSave ? '1' : '0');

        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);

        // Log de la requête AJAX
        console.log('=== REQUÊTE AJAX (UPDATE) ===');
        console.log('URL:', productForm.action);
        console.log('Méthode: POST');
        console.log('FormData:', formData);
        console.log('CSRF Token:', csrfToken);

        // DEBUG: Check validity_months value
        const validityMonthsInput = document.querySelector('input[name="validity_months"]');
        console.log('validity_months input element:', validityMonthsInput);
        console.log('validity_months input value:', validityMonthsInput ? validityMonthsInput.value : 'INPUT NOT FOUND');
        console.log('validity_months in FormData:', formData.get('validity_months'));

        console.log('=== FIN REQUÊTE AJAX (UPDATE) ===');

        // Envoyer la requête AJAX
        fetch(productForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Log de la réponse AJAX dans la console
            console.log('=== RÉPONSE AJAX (UPDATE) ===');
            console.log('Réponse complète:', data);
            if (data.debug) {
                console.log('Debug info:', data.debug);
                console.log('Image générée:', data.debug.image_generated);
                console.log('Image en base:', data.debug.image_in_db);
                console.log('Correspondance:', data.debug.image_match);
            }
            console.log('=== FIN RÉPONSE AJAX (UPDATE) ===');

            if (data.success) {
                // Message adapté selon le mode de sauvegarde
                const title = forceSave ? 'Produit mis à jour en brouillon !' : 'Succès !';
                const text = forceSave ?
                    'Le produit a été mis à jour en mode brouillon. Vous pourrez le finaliser plus tard.' :
                    (data.message || 'Produit mis à jour avec succès !');
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
                    // Rediriger vers la liste des produits
                    window.location.href = data.redirect || '/products';
                });
            } else {
                // Erreur côté serveur
                console.error('Erreur AJAX:', data);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue lors de la mise à jour du produit',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);

            // En cas d'erreur réseau ou autre
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
        if (field.type === 'file') {
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

                // Masquer l'image actuelle quand une nouvelle est sélectionnée
                const currentImageCard = document.getElementById('currentImageCard');
                if (currentImageCard) {
                    currentImageCard.style.display = 'none';
                }

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

    // Fonction pour annuler la sélection d'image
    function clearImageSelection() {
        const imageInput = document.querySelector('input[name="image"]');
        const preview = document.getElementById('imagePreview');
        const currentImageCard = document.getElementById('currentImageCard');

        // Vider le champ de sélection
        if (imageInput) {
            imageInput.value = '';
        }

        // Masquer l'aperçu
        if (preview) {
            preview.style.display = 'none';
        }

        // Réafficher l'image actuelle
        if (currentImageCard) {
            currentImageCard.style.display = 'block';
        }

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
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

    // Charger les données existantes
    loadExistingData();

    console.log('🎉 Système de validation avec Sweet Alert et progression initialisé');
 });

// Fonction pour charger les données existantes
function loadExistingData() {
    const milestonesMap = {};
    @if($product->contentMilestones && $product->contentMilestones->count() > 0)
        @foreach($product->contentMilestones as $milestone)
            milestonesMap[{{ (int) $milestone->product_study_id }}] = {{ (int) $milestone->milestone_month }};
        @endforeach
    @endif

    @if($product->quizzes && $product->quizzes->count() > 0)
        @foreach($product->quizzes as $quiz)
            @php
                $typeTitle = mb_strtolower((string) optional($quiz->type)->titre);
                $isQuiz = str_contains($typeTitle, 'quiz') || (int) $quiz->type_id === 2;
                $isExam = str_contains($typeTitle, 'exam') || str_contains($typeTitle, 'examen') || (int) $quiz->type_id === 1;
            @endphp
            @if($isQuiz)
                addExistingQuizRow(
                    {{ (int) $quiz->id }},
                    @json($quiz->name_ar),
                    @json($quiz->name_en),
                    {{ (int) ($quiz->pivot->score_success ?? 50) }},
                    {{ (int) ($quiz->pivot->nb_question_affiche ?? 10) }},
                    @json($quiz->pivot->installment_month),
                    @json($quiz->pivot->opens_after_purchase_days ?? '')
                );
            @elseif($isExam)
                addExistingExamRow(
                    {{ (int) $quiz->id }},
                    @json($quiz->name_ar),
                    @json($quiz->name_en),
                    {{ (int) ($quiz->pivot->score_success ?? 50) }},
                    {{ (int) ($quiz->pivot->nb_question_affiche ?? 10) }},
                    'theoretical',
                    {{ (int) ($quiz->pivot->use_own_questions ?? 0) }},
                    @json($quiz->pivot->installment_month),
                    @json($quiz->pivot->opens_after_purchase_days ?? '')
                );
            @else
                addExistingQuizRow(
                    {{ (int) $quiz->id }},
                    @json($quiz->name_ar),
                    @json($quiz->name_en),
                    {{ (int) ($quiz->pivot->score_success ?? 50) }},
                    {{ (int) ($quiz->pivot->nb_question_affiche ?? 10) }},
                    @json($quiz->pivot->installment_month),
                    @json($quiz->pivot->opens_after_purchase_days ?? '')
                );
            @endif
        @endforeach
    @endif

    @if($product->trainingCases && $product->trainingCases->count() > 0)
        @foreach($product->trainingCases as $trainingCase)
            addExistingExamRow(
                @json('tc_' . $trainingCase->id),
                @json($trainingCase->name),
                @json($trainingCase->name),
                50,
                10,
                'practical',
                'online',
                null,
                null
            );
        @endforeach
    @endif

    @if($product->studies && $product->studies->count() > 0)
        @foreach($product->studies as $study)
            @if($study->resource)
                addExistingStudyRow(
                    {{ (int) $study->resource->id }},
                    @json($study->resource->name_ar),
                    @json($study->resource->type),
                    @json($study->resource->file),
                    {{ (int) ($study->order ?? 0) }},
                    milestonesMap[{{ (int) $study->id }}] || 0,
                    @json($study->opens_after_purchase_days ?? '')
                );
            @endif
        @endforeach
    @endif

    // Show column headers if studies exist
    checkStudyContainer();
}

// Fonction pour ajouter une ligne de quiz existante
function addExistingQuizRow(quizId, nameAr, nameEn, scoreSuccess, nbQuestions, installmentMonth, opensAfterPurchaseDays) {
    const container = document.getElementById('quizContainer');
    const noQuizMessage = document.getElementById('noQuizMessage');

    // Masquer le message "Aucun quiz sélectionné"
    if (noQuizMessage) {
        noQuizMessage.style.display = 'none';
    }

    const quizDaysStr = (opensAfterPurchaseDays !== undefined && opensAfterPurchaseDays !== null && opensAfterPurchaseDays !== '')
        ? String(opensAfterPurchaseDays)
        : '';

    // Créer la ligne de quiz
    const quizRow = document.createElement('div');
    quizRow.className = 'quiz-row mb-3 p-3 border rounded bg-light';
    quizRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i data-feather="help-circle" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
                    <div>
                        <strong>${nameAr}</strong>
                        <br>
                        <small class="text-muted">${nameEn}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Score /100</label>
                <input type="number" name="existing_quiz_scores[${quizId}]" class="form-control form-control-sm" min="0" max="100" value="${scoreSuccess}" required>
                <label class="form-label small mb-0 mt-1">Questions</label>
                <input type="number" name="existing_quiz_nb_questions[${quizId}]" class="form-control form-control-sm" min="1" value="${nbQuestions}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Month</label>
                <select name="existing_quiz_installment_months[${quizId}]" class="form-select form-select-sm" required>
                    ${window.getInstallmentMonthOptions(installmentMonth)}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Days after access</label>
                <input type="number" name="existing_quiz_opens_after_purchase_days[${quizId}]" class="form-control form-control-sm" min="1" max="3650" placeholder="Optional" value="${quizDaysStr}">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuizRow(this)">
                    <i data-feather="trash-2" class="me-1"></i>
                    Supprimer
                </button>
            </div>
        </div>
        <input type="hidden" name="existing_quiz_ids[]" value="${quizId}">
    `;

    container.appendChild(quizRow);

    // Réinitialiser Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Fonction pour supprimer une ligne de quiz existante
function removeQuizRow(button) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce quiz du produit ?')) {
        const quizRow = button.closest('.quiz-row');
        const quizId = quizRow.querySelector('input[name="existing_quiz_ids[]"]').value;

        // Supprimer via AJAX
        fetch(`{{ route('products.quizzes.destroy', [$product, '__QUIZ_ID__']) }}`.replace('__QUIZ_ID__', quizId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Supprimer la ligne de l'interface
                quizRow.remove();

                // Vérifier s'il faut afficher le message "Aucun quiz"
                checkQuizContainer();

                // Afficher un message de succès
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: 'Quiz supprimé avec succès',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Quiz supprimé avec succès');
                }
            } else {
                alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression du quiz');
        });
    }
}

// Fonction pour supprimer une ligne d'examen existante
function removeExamRow(button) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet examen du produit ?')) {
        const examRow = button.closest('.exam-row');
        const examId = examRow.querySelector('input[name="existing_exam_ids[]"]').value;

        // Supprimer via AJAX
        fetch(`{{ route('products.quizzes.destroy', [$product, '__EXAM_ID__']) }}`.replace('__EXAM_ID__', examId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Supprimer la ligne de l'interface
                examRow.remove();

                // Vérifier s'il faut afficher le message "Aucun examen"
                checkExamContainer();

                // Afficher un message de succès
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: 'Examen supprimé avec succès',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Examen supprimé avec succès');
                }
            } else {
                alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression de l\'examen');
        });
    }
}

// Fonction pour ajouter une ligne d'examen existante
function addExistingExamRow(examId, nameAr, nameEn, scoreSuccess, nbQuestions, examType, useOwnQuestions, installmentMonth, opensAfterPurchaseDays) {
    const container = document.getElementById('examContainer');
    const noExamMessage = document.getElementById('noExamMessage');

    if (noExamMessage) {
        noExamMessage.style.display = 'none';
    }

    const examDaysStr = (opensAfterPurchaseDays !== undefined && opensAfterPurchaseDays !== null && opensAfterPurchaseDays !== '')
        ? String(opensAfterPurchaseDays)
        : '';

    const examRow = document.createElement('div');
    examRow.className = 'exam-row mb-3 p-3 border rounded bg-light';
    examRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i data-feather="book-open" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
                    <div>
                        <strong>${nameAr}</strong>
                        <br>
                        <small class="text-muted">${nameEn}</small>
                    </div>
                </div>
            </div>
            ${examType === 'theoretical'
                ? `
            <div class="col-md-2">
                <label class="form-label small mb-0">Score /100</label>
                <input type="number" name="existing_exam_scores[${examId}]" class="form-control form-control-sm" min="0" max="100" value="${scoreSuccess}" required>
                <label class="form-label small mb-0 mt-1">Questions</label>
                <input type="number" name="existing_exam_nb_questions[${examId}]" class="form-control form-control-sm" min="1" value="${nbQuestions}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Month</label>
                <select name="existing_exam_installment_months[${examId}]" class="form-select form-select-sm" required>
                    ${window.getInstallmentMonthOptions(installmentMonth)}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Days after access</label>
                <input type="number" name="existing_exam_opens_after_purchase_days[${examId}]" class="form-control form-control-sm" min="1" max="3650" placeholder="Optional" value="${examDaysStr}">
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExamRow(this)">
                    <i data-feather="trash-2" class="me-1"></i>
                    Supprimer
                </button>
            </div>`
                : `
            <div class="col-md-2">
                <div>
                    <span class="badge bg-success me-2">${scoreSuccess}/100</span>
                    <span class="badge bg-secondary">${nbQuestions} questions</span>
                </div>
            </div>
            <input type="hidden" name="existing_exam_installment_months[${examId}]" value="">
            <div class="col-md-7 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExamRow(this)">
                    <i data-feather="trash-2" class="me-1"></i>
                    Supprimer
                </button>
            </div>
            `}
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="existing_exam_use_own_questions[${examId}]" value="0">
                    <input class="form-check-input" type="checkbox" name="existing_exam_use_own_questions[${examId}]" value="1" id="existing_exam_use_own_${examId}" ${useOwnQuestions == 1 ? 'checked' : ''}>
                    <label class="form-check-label" for="existing_exam_use_own_${examId}">
                        Use Exam's Own Questions (if unchecked, will fetch from course quizzes)
                    </label>
                </div>
            </div>
        </div>
        <input type="hidden" name="existing_exam_ids[]" value="${examId}">
    `;

    container.appendChild(examRow);

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Fonction pour ajouter une ligne d'étude existante
function addExistingStudyRow(resourceId, title, type, fileUrl, order, milestoneMonth, opensAfterDays) {
    const container = document.getElementById('studyContainer');
    const noStudyMessage = document.getElementById('noStudyMessage');

    // Masquer le message "Aucune étude sélectionnée"
    if (noStudyMessage) {
        noStudyMessage.style.display = 'none';
    }

    // Get current row count for order
    const currentOrder = order !== undefined ? order : container.querySelectorAll('.study-row').length;

    // Build month options
    const maxMonths = parseInt(document.querySelector('input[name="validity_months"]')?.value) || 12;
    let monthOptions = '<option value="0">-- No Month --</option>';
    for (let i = 1; i <= maxMonths; i++) {
        const selected = (milestoneMonth && milestoneMonth == i) ? 'selected' : '';
        monthOptions += `<option value="${i}" ${selected}>Month ${i}</option>`;
    }

    const daysVal = (opensAfterDays !== undefined && opensAfterDays !== null && opensAfterDays !== '')
        ? String(opensAfterDays)
        : '';

    // Créer la ligne d'étude
    const studyRow = document.createElement('div');
    studyRow.className = 'study-row border rounded p-3 mb-3';
    studyRow.setAttribute('data-resource-id', resourceId);
    studyRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-1">
                <div class="study-drag-handle text-center text-muted">
                    <i data-feather="menu" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i data-feather="book-open" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                    <div>
                        <strong>${title}</strong>
                        <br>
                        <small class="text-muted">Type: ${type}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <select name="content_milestones[${resourceId}]" class="form-select form-select-sm milestone-month-select">
                    ${monthOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">Days after access</label>
                <input type="number" name="content_opens_after_days[${resourceId}]" class="form-control form-control-sm" min="1" max="3650" placeholder="Optional" value="${daysVal}">
            </div>
            <div class="col-md-2">
                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-4">
                    <i data-feather="download" class="me-1"></i>
                    Télécharger
                </a>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger mt-4" onclick="removeStudyRow(this)">
                    <i data-feather="trash-2" class="me-1"></i>
                    Supprimer
                </button>
            </div>
        </div>
        <input type="hidden" name="study_items[]" value="${resourceId}" class="study-resource-id-input">
        <input type="hidden" class="study-order-input" data-resource-id="${resourceId}">
    `;

    container.appendChild(studyRow);

    // Réinitialiser Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
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

    // Initialiser tous les types de selects (excluding study-resource-select)
    const selectTypes = [
        { selector: '.quiz-select', placeholder: 'Rechercher et sélectionner un quiz...' },
        { selector: '.exam-select', placeholder: 'Rechercher et sélectionner un examen...' }
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
    const selectTypes = ['.quiz-select', '.exam-select'];

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

<!-- Stage Documents Upload Script -->
<script>
// Toggle Stage Documents Section visibility
document.addEventListener('DOMContentLoaded', function() {
    const stageCheckbox = document.getElementById('is_stage');
    const stageDocumentsSection = document.getElementById('stageDocumentsSection');

    if (stageCheckbox && stageDocumentsSection) {
        stageCheckbox.addEventListener('change', function() {
            stageDocumentsSection.style.display = this.checked ? 'block' : 'none';
        });
    }
});

function uploadDocument(input, documentType) {
    if (!input.files || !input.files[0]) return;

    const productId = {{ $product->id }};
    const file = input.files[0];
    const formData = new FormData();

    formData.append('product_id', productId);
    formData.append('document_type', documentType);
    formData.append('file', file);

    const btn = input.closest('.mb-4').querySelector('button');
    const originalText = btn ? btn.innerText : '';

    fetch('{{ route("stage-documents.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Document uploaded successfully',
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error uploading document'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error uploading document: ' + error.message
        });
    });
}

function deleteDocument(documentId) {
    Swal.fire({
        title: 'Confirm Delete',
        text: 'Are you sure you want to delete this document?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/stage-documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Server returned ${response.status}: ${text.substring(0, 200)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Document deleted successfully',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error deleting document'
                    });
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error deleting document'
                });
            });
        }
    });
}

// Function to remove a study row
function removeStudyRow(button) {
    if (!confirm('Êtes-vous sûr de vouloir retirer cette ressource du cours ?')) {
        return;
    }

    const studyRow = button.closest('.study-row');
    if (!studyRow) {
        console.error('Study row not found');
        return;
    }

    // Get the resource ID from the hidden input
    const hiddenInput = studyRow.querySelector('input[name="existing_study_ids[]"]');
    const resourceId = hiddenInput ? hiddenInput.value : null;

    if (!resourceId) {
        console.error('Resource ID not found');
        studyRow.remove();
        return;
    }

    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i data-feather="loader" class="me-1"></i> Suppression...';

    // Get product ID from the page URL or form
    const productId = {{ $product->id ?? 'null' }};

    if (!productId) {
        alert('Product ID not found');
        button.disabled = false;
        return;
    }

    // Call API to remove the resource
    fetch(`/products/${productId}/remove-study-resource`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            resource_id: resourceId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from DOM
            studyRow.remove();

            // Check if container is empty and show message
            const container = document.getElementById('studyContainer');
            const noStudyMessage = document.getElementById('noStudyMessage');
            const remainingRows = container.querySelectorAll('.study-row');

            if (remainingRows.length === 0 && noStudyMessage) {
                noStudyMessage.style.display = 'block';
            }

            console.log('Study resource removed successfully');

            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } else {
            alert('Erreur: ' + data.message);
            button.disabled = false;
            button.innerHTML = '<i data-feather="trash-2" class="me-1"></i> Supprimer';
            if (typeof feather !== 'undefined') feather.replace();
        }
    })
    .catch(error => {
        console.error('Error removing study resource:', error);
        alert('Erreur lors de la suppression de la ressource');
        button.disabled = false;
        button.innerHTML = '<i data-feather="trash-2" class="me-1"></i> Supprimer';
        if (typeof feather !== 'undefined') feather.replace();
    });
}
</script>

<!-- SortableJS for drag and drop ordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Function to update hidden order inputs - defined globally
window.updateStudyOrder = function() {
    const container = document.getElementById('studyContainer');
    const rows = container.querySelectorAll('.study-row');
    console.log('📋 Updating study order for', rows.length, 'rows');
    rows.forEach((row, index) => {
        const orderInput = row.querySelector('.study-order-input');
        if (orderInput) {
            orderInput.value = index;
            console.log('  Row', index, '- Resource:', row.getAttribute('data-resource-id'), '- Order:', index);
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize SortableJS for study resources
    const studyContainer = document.getElementById('studyContainer');
    if (studyContainer) {
        window.studySortable = new Sortable(studyContainer, {
            animation: 150,
            handle: '.study-drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            filter: '#noStudyMessage',
            onEnd: function(evt) {
                window.updateStudyOrder();
                console.log('✅ Drag ended - order updated');
            }
        });
        console.log('✅ SortableJS initialized for study resources');
    }

    // Hook into form submission to ensure order is updated
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            window.updateStudyOrder();
            console.log('📤 Form submitting with updated study order');
        }, true); // Use capture phase to run first
    }
});
</script>

<style>
.sortable-ghost {
    opacity: 0.4;
    background-color: #e3f2fd;
}
.sortable-chosen {
    background-color: #bbdefb;
}
.study-drag-handle {
    cursor: grab;
    padding: 5px;
}
.study-drag-handle:active {
    cursor: grabbing;
}
.study-row {
    transition: transform 0.15s ease;
}
</style>

@endsection
