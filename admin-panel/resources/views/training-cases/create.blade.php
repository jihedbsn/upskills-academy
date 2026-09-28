@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Add Training Case</h4>
                                <p class="text-white-50 mb-0">Create a new training scenario</p>
                            </div>
                        </div>
                        <a href="{{ route('training-cases.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validation Errors:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('training-cases.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Training Case Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="5"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Upload Files (PDF, Images) <span class="text-danger">*</span></label>
                            <input type="file"
                                   class="form-control @error('files.*') is-invalid @enderror"
                                   id="files"
                                   name="files[]"
                                   accept=".pdf,.jpg,.jpeg,.png,.gif"
                                   multiple
                                   required>
                            <small class="text-muted">Max size: 10MB per file. Allowed formats: PDF, JPG, PNG, GIF. You can select multiple files.</small>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- File preview -->
                            <div id="filePreview" class="mt-3"></div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (available for selection in exams)
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('training-cases.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i data-feather="save" class="me-1"></i> Create Training Case
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
<script>
    feather.replace();

    // File preview
    document.getElementById('files').addEventListener('change', function(e) {
        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';

        if (this.files.length > 0) {
            const list = document.createElement('div');
            list.className = 'alert alert-info';
            list.innerHTML = '<strong>Selected Files:</strong><ul class="mb-0 mt-2">';

            Array.from(this.files).forEach(file => {
                const size = (file.size / 1024).toFixed(2);
                list.innerHTML += `<li>${file.name} (${size} KB)</li>`;
            });

            list.innerHTML += '</ul>';
            preview.appendChild(list);
        }
    });
</script>
@endpush
