@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Create New Carousel</h4>
                            <p class="text-white-50 mb-0">Add a new carousel with multilingual content</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <h6 class="mb-1">Validation Errors</h6>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('carousels.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
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
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="slug_ar" class="form-label">Arabic Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('slug_ar') is-invalid @enderror" 
                                                           id="slug_ar" name="slug_ar" value="{{ old('slug_ar') }}" 
                                                           placeholder="Enter the Arabic slug">
                                                    @error('slug_ar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="description_ar" class="form-label">Arabic Description <span class="text-danger">*</span></label>
                                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                                              id="description_ar" name="description_ar" rows="4" 
                                                              placeholder="Enter the Arabic description">{{ old('description_ar') }}</textarea>
                                                    @error('description_ar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
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
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="slug_en" class="form-label">English Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('slug_en') is-invalid @enderror" 
                                                           id="slug_en" name="slug_en" value="{{ old('slug_en') }}" 
                                                           placeholder="Enter the English slug">
                                                    @error('slug_en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="description_en" class="form-label">Description Anglaise <span class="text-danger">*</span></label>
                                                    <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                                              id="description_en" name="description_en" rows="4" 
                                                              placeholder="Entrez la description en anglais">{{ old('description_en') }}</textarea>
                                                    @error('description_en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Image et Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="image" class="me-2"></i>
                                            Image et Options
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Image</label>
                                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                           id="image" name="image" accept="image/*">
                                                    <div class="form-text">Formats acceptés: JPG, PNG, GIF, WebP (max 2MB)</div>
                                                    @error('image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="order" class="form-label">Ordre d'affichage</label>
                                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                                           id="order" name="order" value="{{ old('order', 0) }}" 
                                                           min="0" placeholder="0">
                                                    @error('order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">
                                                            Actif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('carousels.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save" class="me-2"></i>
                                        Créer le carousel
                                    </button>
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
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.modern-alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
}

.form-control:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
}

.form-check-input:checked {
    background-color: #7367f0;
    border-color: #7367f0;
}
</style>
@endsection
