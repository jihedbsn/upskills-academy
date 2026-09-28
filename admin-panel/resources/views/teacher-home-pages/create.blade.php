@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa fa-plus text-white" style="font-size: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Add New Teacher to Homepage</h4>
                                <p class="text-white-50 mb-0">Fill in the teacher information</p>
                            </div>
                        </div>
                        <a href="{{ route('teacher-home-pages.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('teacher-home-pages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Name Arabic -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">
                                        <i class="fa fa-language me-1"></i>
                                        <strong>Name (Arabic)</strong>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name_ar') is-invalid @enderror" 
                                           id="name_ar" 
                                           name="name_ar" 
                                           value="{{ old('name_ar') }}" 
                                           placeholder="أدخل اسم المدرس بالعربية"
                                           dir="rtl"
                                           required>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Name English -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">
                                        <i class="fa fa-language me-1"></i>
                                        <strong>Name (English)</strong>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" 
                                           name="name_en" 
                                           value="{{ old('name_en') }}" 
                                           placeholder="Enter teacher name in English"
                                           required>
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">
                                        <i class="fa fa-image me-1"></i>
                                        <strong>Teacher Image</strong>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                                </div>
                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="order" class="form-label">
                                        <i class="fa fa-sort me-1"></i>
                                        <strong>Display Order</strong>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('order') is-invalid @enderror" 
                                           id="order" 
                                           name="order" 
                                           value="{{ old('order', 0) }}" 
                                           min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label d-block">
                                        <i class="fa fa-toggle-on me-1"></i>
                                        <strong>Status</strong>
                                    </label>
                                    <div class="form-check form-switch" style="font-size: 1.2rem; padding-top: 0.3rem;">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('teacher-home-pages.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save me-2"></i>
                                        Save Teacher
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

.gap-2 {
    gap: 0.5rem;
}
</style>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        const imagePreview = document.getElementById('imagePreview');
        preview.src = reader.result;
        imagePreview.style.display = 'block';
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>
@endsection

