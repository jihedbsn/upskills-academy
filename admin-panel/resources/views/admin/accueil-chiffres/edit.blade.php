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
                            <h4 class="card-title mb-1">Edit Homepage Statistics</h4>
                            <p class="text-white-50 mb-0">Update statistics and icons for the homepage</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="mb-1">Validation Errors</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('accueil-chiffres.update', $accueilChiffre) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Coach Ready Section -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="users" class="me-2"></i>
                                            Coach Ready
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="coach_ready" class="form-label">Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="coach_ready" name="coach_ready" 
                                                   value="{{ old('coach_ready', $accueilChiffre->coach_ready) }}" min="0" required>
                                        </div>
                                        @if($accueilChiffre->icone_coach_ready)
                                            <div class="mb-3">
                                                <label class="form-label">Current Icon</label>
                                                <div class="mt-2">
                                                    <img src="{{ $accueilChiffre->icone_coach_ready_url }}" 
                                                         alt="Current Coach Ready Icon" class="img-thumbnail" style="max-width: 100px;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label for="icone_coach_ready" class="form-label">New Icon</label>
                                            <input type="file" class="form-control" id="icone_coach_ready" name="icone_coach_ready" 
                                                   accept="image/*">
                                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP (max 2MB)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Book of Academy Section -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="book" class="me-2"></i>
                                            Book of the Academy
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="book_of_the_academy" class="form-label">Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="book_of_the_academy" name="book_of_the_academy" 
                                                   value="{{ old('book_of_the_academy', $accueilChiffre->book_of_the_academy) }}" min="0" required>
                                        </div>
                                        @if($accueilChiffre->icone_book_of_the_academy)
                                            <div class="mb-3">
                                                <label class="form-label">Current Icon</label>
                                                <div class="mt-2">
                                                    <img src="{{ $accueilChiffre->icone_book_of_the_academy_url }}" 
                                                         alt="Current Book Icon" class="img-thumbnail" style="max-width: 100px;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label for="icone_book_of_the_academy" class="form-label">New Icon</label>
                                            <input type="file" class="form-control" id="icone_book_of_the_academy" name="icone_book_of_the_academy" 
                                                   accept="image/*">
                                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP (max 2MB)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registered Students Section -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="user-check" class="me-2"></i>
                                            Registered Students
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="registered_student" class="form-label">Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="registered_student" name="registered_student" 
                                                   value="{{ old('registered_student', $accueilChiffre->registered_student) }}" min="0" required>
                                        </div>
                                        @if($accueilChiffre->icone_registered_student)
                                            <div class="mb-3">
                                                <label class="form-label">Current Icon</label>
                                                <div class="mt-2">
                                                    <img src="{{ $accueilChiffre->icone_registered_student_url }}" 
                                                         alt="Current Student Icon" class="img-thumbnail" style="max-width: 100px;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label for="icone_registered_student" class="form-label">New Icon</label>
                                            <input type="file" class="form-control" id="icone_registered_student" name="icone_registered_student" 
                                                   accept="image/*">
                                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP (max 2MB)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Training Programs Section -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="award" class="me-2"></i>
                                            Training Programs
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="training_program" class="form-label">Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="training_program" name="training_program" 
                                                   value="{{ old('training_program', $accueilChiffre->training_program) }}" min="0" required>
                                        </div>
                                        @if($accueilChiffre->icone_training_program)
                                            <div class="mb-3">
                                                <label class="form-label">Current Icon</label>
                                                <div class="mt-2">
                                                    <img src="{{ $accueilChiffre->icone_training_program_url }}" 
                                                         alt="Current Training Icon" class="img-thumbnail" style="max-width: 100px;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label for="icone_training_program" class="form-label">New Icon</label>
                                            <input type="file" class="form-control" id="icone_training_program" name="icone_training_program" 
                                                   accept="image/*">
                                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WebP (max 2MB)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="settings" class="me-2"></i>
                                            Status
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', $accueilChiffre->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('accueil-chiffres.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Update Statistics
                            </button>
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

.form-control:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
}

.btn {
    border-radius: 6px;
}
</style>
@endsection
