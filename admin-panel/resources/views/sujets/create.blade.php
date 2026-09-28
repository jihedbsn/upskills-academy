@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="page-title">Create Subject</h1>
                            <p class="text-muted mb-0">Create a new exam subject</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('sujets.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">
                                        <i data-feather="tag" class="me-1"></i>
                                        Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Select a course type</option>
                                        <option value="fa" {{ old('type') == 'fa' ? 'selected' : '' }}>Foundation English (FA)</option>
                                        <option value="fi" {{ old('type') == 'fi' ? 'selected' : '' }}>Fluency &amp; Interaction (FI)</option>
                                        <option value="pt" {{ old('type') == 'pt' ? 'selected' : '' }}>Proficiency Track (PT)</option>
                                        <option value="autres" {{ old('type') == 'autres' ? 'selected' : '' }}>Autres</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lang" class="form-label">
                                        <i data-feather="globe" class="me-1"></i>
                                        Language <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('lang') is-invalid @enderror" id="lang" name="lang" required>
                                        <option value="">-- Choose a language --</option>
                                        <option value="ar" {{ old('lang') == 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ old('lang') == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                    @error('lang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i data-feather="file-text" class="me-1"></i>
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="8" 
                                              placeholder="Enter the subject description..." required>{{ old('description') }}</textarea>
                                    <div class="form-text">
                                        Maximum 5000 characters. 
                                        <span id="char-count">0</span>/5000
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('sujets.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i data-feather="save" class="me-2"></i>
                                        Create Subject
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
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();

    // Character counter
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');

    descriptionTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 5000) {
            charCount.style.color = 'red';
        } else if (length > 4500) {
            charCount.style.color = 'orange';
        } else {
            charCount.style.color = 'inherit';
        }
    });

    // Initialize counter on page load
    charCount.textContent = descriptionTextarea.value.length;
</script>
@endpush
