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
                            <h4 class="card-title mb-1">Edit Quiz Result</h4>
                            <p class="text-white-50 mb-0">Edit the information for result #{{ $resultatQuiz->id }}</p>
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

                    <form action="{{ route('admin.resultat-quizzes.update', $resultatQuiz) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations de l'étudiant -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="user" class="me-2"></i>
                                            Student Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                                <select class="form-select @error('student_id') is-invalid @enderror" 
                                                        id="student_id" name="student_id" required>
                                                    <option value="">Select a student</option>
                                                    @foreach($students as $student)
                                                        <option value="{{ $student->id }}" 
                                                                {{ old('student_id', $resultatQuiz->student_id) == $student->id ? 'selected' : '' }}>
                                                            {{ $student->full_name }} ({{ $student->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('student_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Current Student</label>
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-content">{{ $resultatQuiz->student->initials }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $resultatQuiz->student->full_name }}</h6>
                                                        <small class="text-muted">{{ $resultatQuiz->student->email }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du cours et quiz -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="book-open" class="me-2"></i>
                                            Course and Quiz
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="product_id" class="form-label">Course <span class="text-danger">*</span></label>
                                                <select class="form-select @error('product_id') is-invalid @enderror" 
                                                        id="product_id" name="product_id" required>
                                                    <option value="">Select a course</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                                {{ old('product_id', $resultatQuiz->product_id) == $product->id ? 'selected' : '' }}>
                                                            {{ $product->titre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="quiz_id" class="form-label">Quiz <span class="text-danger">*</span></label>
                                                <select class="form-select @error('quiz_id') is-invalid @enderror" 
                                                        id="quiz_id" name="quiz_id" required>
                                                    <option value="">Select a quiz</option>
                                                    @foreach($quizzes as $quiz)
                                                        <option value="{{ $quiz->id }}" 
                                                                {{ old('quiz_id', $resultatQuiz->quiz_id) == $quiz->id ? 'selected' : '' }}>
                                                            {{ $quiz->name_en }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('quiz_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Résultats du quiz -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="award" class="me-2"></i>
                                            Quiz Results
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="score" class="form-label">Score (%) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('score') is-invalid @enderror" 
                                                       id="score" name="score" min="0" max="100" step="0.01"
                                                       value="{{ old('score', $resultatQuiz->score) }}" required>
                                                <div class="form-text">Score between 0 and 100</div>
                                                @error('score')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="attempts" class="form-label">Number of Attempts <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('attempts') is-invalid @enderror" 
                                                       id="attempts" name="attempts" min="1" max="10"
                                                       value="{{ old('attempts', $resultatQuiz->attempts) }}" required>
                                                <div class="form-text">Number of attempts (1-10)</div>
                                                @error('attempts')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="success" name="success" value="1" 
                                                           {{ old('success', $resultatQuiz->success) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="success">
                                                        Quiz Passed
                                                    </label>
                                                </div>
                                                <div class="form-text">Checked if the quiz is passed (score ≥ 50%)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations actuelles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="info" class="me-2"></i>
                                            Current Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Result ID</label>
                                                <input type="text" class="form-control" value="{{ $resultatQuiz->id }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Current Course</label>
                                                <input type="text" class="form-control" value="{{ $resultatQuiz->product->titre }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Current Quiz</label>
                                                <input type="text" class="form-control" value="{{ $resultatQuiz->quiz->name_en }}" readonly>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Creation Date</label>
                                                <input type="text" class="form-control" value="{{ $resultatQuiz->created_at->format('d/m/Y H:i') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Back to List
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.resultat-quizzes.show', $resultatQuiz) }}" class="btn btn-info">
                                            <i data-feather="eye" class="me-2"></i>
                                            View Result
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

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #7367f0;
    color: white;
    font-weight: bold;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}
</style>

<script>
// Calculer automatiquement le succès basé sur le score
document.getElementById('score').addEventListener('input', function() {
    const score = parseFloat(this.value);
    const successCheckbox = document.getElementById('success');
    
    if (score >= 50) {
        successCheckbox.checked = true;
    } else {
        successCheckbox.checked = false;
    }
});

// Validation du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    const score = parseFloat(document.getElementById('score').value);
    const attempts = parseInt(document.getElementById('attempts').value);
    
    if (score < 0 || score > 100) {
        e.preventDefault();
        alert('The score must be between 0 and 100');
        return false;
    }
    
    if (attempts < 1 || attempts > 10) {
        e.preventDefault();
        alert('The number of attempts must be between 1 and 10');
        return false;
    }
});
</script>
@endsection
