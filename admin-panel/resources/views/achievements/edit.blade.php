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
                                <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Edit Achievements</h4>
                                <p class="text-white-50 mb-0">Update academy statistics</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Back to Achievements
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle" class="me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.achievements.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Training Programs -->
                            <div class="col-md-6">
                                <div class="statistic-input-card">
                                    <div class="statistic-icon bg-primary">
                                        <i data-feather="book-open"></i>
                                    </div>
                                    <div class="statistic-form-group">
                                        <label for="training_programs" class="form-label">
                                            <strong>Training Programs</strong>
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-lg @error('training_programs') is-invalid @enderror" 
                                               id="training_programs" 
                                               name="training_programs" 
                                               value="{{ old('training_programs', $achievement->training_programs) }}" 
                                               min="0" 
                                               required>
                                        @error('training_programs')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Total number of training programs offered</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Registered Students -->
                            <div class="col-md-6">
                                <div class="statistic-input-card">
                                    <div class="statistic-icon bg-success">
                                        <i data-feather="users"></i>
                                    </div>
                                    <div class="statistic-form-group">
                                        <label for="registered_students" class="form-label">
                                            <strong>Registered Students</strong>
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-lg @error('registered_students') is-invalid @enderror" 
                                               id="registered_students" 
                                               name="registered_students" 
                                               value="{{ old('registered_students', $achievement->registered_students) }}" 
                                               min="0" 
                                               required>
                                        @error('registered_students')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Total number of registered students</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Academy Books -->
                            <div class="col-md-6">
                                <div class="statistic-input-card">
                                    <div class="statistic-icon bg-info">
                                        <i data-feather="book"></i>
                                    </div>
                                    <div class="statistic-form-group">
                                        <label for="academy_books" class="form-label">
                                            <strong>Academy Books</strong>
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-lg @error('academy_books') is-invalid @enderror" 
                                               id="academy_books" 
                                               name="academy_books" 
                                               value="{{ old('academy_books', $achievement->academy_books) }}" 
                                               min="0" 
                                               required>
                                        @error('academy_books')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Total number of books in the academy</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Ready Instructors -->
                            <div class="col-md-6">
                                <div class="statistic-input-card">
                                    <div class="statistic-icon bg-warning">
                                        <i data-feather="user-check"></i>
                                    </div>
                                    <div class="statistic-form-group">
                                        <label for="ready_instructors" class="form-label">
                                            <strong>Ready Instructors</strong>
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-lg @error('ready_instructors') is-invalid @enderror" 
                                               id="ready_instructors" 
                                               name="ready_instructors" 
                                               value="{{ old('ready_instructors', $achievement->ready_instructors) }}" 
                                               min="0" 
                                               required>
                                        @error('ready_instructors')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Total number of ready instructors</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">
                                        <i data-feather="x" class="me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i data-feather="save" class="me-2"></i>
                                        Save Changes
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
.statistic-input-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: start;
    gap: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.statistic-input-card:hover {
    border-color: #7367f0;
    box-shadow: 0 2px 8px rgba(115, 103, 240, 0.15);
}

.statistic-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.statistic-icon i {
    width: 24px;
    height: 24px;
    color: white;
}

.statistic-form-group {
    flex: 1;
}

.form-control-lg {
    font-size: 1.25rem;
    font-weight: 600;
}

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
@endsection

@section('scripts')
<script>
// Initialize Feather icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection

