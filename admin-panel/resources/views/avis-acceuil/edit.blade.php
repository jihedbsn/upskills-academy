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
                            <h4 class="card-title mb-1">Edit customer testimonial</h4>
                            <p class="text-white-50 mb-0">Edit the testimonial of "{{ $avisAcceuil->client }}"</p>
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

                    <form action="{{ route('avis-acceuil.update', $avisAcceuil) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer information section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="user" class="me-2"></i>
                                            Customer Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="client" class="form-label">Client name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('client') is-invalid @enderror" 
                                                           id="client" name="client" value="{{ old('client', $avisAcceuil->client) }}" 
                                                           placeholder="Enter the client name">
                                                    @error('client')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Client photo</label>
                                                    @if($avisAcceuil->image)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('uploads/avis/' . $avisAcceuil->image) }}" 
                                                                 alt="Current photo" class="img-thumbnail" style="max-width: 200px;">
                                                            <p class="text-muted small mt-1">Current photo</p>
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                           id="image" name="image" accept="image/*">
                                                    <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP (max 2MB)</div>
                                                    @error('image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Multilingual testimonial section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="message-square" class="me-2"></i>
                                            Customer Testimonial
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="avis_en" class="form-label">Testimonial in English <span class="text-danger">*</span></label>
                                                    <textarea class="form-control @error('avis_en') is-invalid @enderror" 
                                                              id="avis_en" name="avis_en" rows="4" 
                                                              placeholder="Enter the testimonial in English">{{ old('avis_en', $avisAcceuil->avis_en) }}</textarea>
                                                    @error('avis_en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="avis_ar" class="form-label">Testimonial in Arabic <span class="text-danger">*</span></label>
                                                    <textarea class="form-control @error('avis_ar') is-invalid @enderror" 
                                                              id="avis_ar" name="avis_ar" rows="4" 
                                                              placeholder="Enter the testimonial in Arabic">{{ old('avis_ar', $avisAcceuil->avis_ar) }}</textarea>
                                                    @error('avis_ar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Options section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="settings" class="me-2"></i>
                                            Display Options
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="order" class="form-label">Display order</label>
                                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                                           id="order" name="order" value="{{ old('order', $avisAcceuil->order) }}" 
                                                           min="0" placeholder="0">
                                                    @error('order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                               {{ old('is_active', $avisAcceuil->is_active) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('avis-acceuil.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i data-feather="save" class="me-2"></i>
                                        Update
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

.img-thumbnail {
    border-radius: 8px;
}
</style>
@endsection
