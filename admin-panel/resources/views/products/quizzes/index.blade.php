@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Quizzes & Exams - {{ $product->id }}</h1>
        <div class="page-actions">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Back to Products
            </a>
        </div>
    </div>

    <!-- Product information -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="package" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Product #{{ $product->id }}</h4>
                            <p class="text-white-50 mb-0">Manage associated quizzes and exams</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success modern-alert">
            <div class="d-flex align-items-center">
                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Quiz Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Quiz</h4>
                            <p class="text-white-50 mb-0">Quizzes associated with the product</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quiz search filter -->
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="quizSearch" class="form-control" placeholder="Search quiz...">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchQuizzes('quiz')">
                                <i data-feather="search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- List of associated quizzes -->
                    <div id="quizList">
                        @php
                            $quizQuizzes = $productQuizzes->filter(function($quiz) {
                                return stripos($quiz->type->titre, 'quiz') !== false;
                            });
                        @endphp
                        
                        @if($quizQuizzes->count() > 0)
                            <div class="list-group">
                                @foreach($quizQuizzes as $quiz)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $quiz->name_ar }}</h6>
                                            <small class="text-muted">{{ $quiz->name_en }}</small>
                                            <br>
                                            <span class="badge bg-light-info">{{ $quiz->type->titre }}</span>
                                            <span class="badge bg-light-primary">{{ $quiz->score }}/100</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <form action="{{ route('products.quizzes.destroy', [$product, $quiz]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                <p class="text-muted mt-2">No quizzes associated</p>
                            </div>
                        @endif
                    </div>

                    <!-- Add a quiz -->
                    <div class="mt-3">
                        <button class="btn btn-success w-100" onclick="showAddQuizModal('quiz')">
                            <i data-feather="plus" class="me-2"></i>
                            Add a Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exams Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Exams</h4>
                            <p class="text-white-50 mb-0">Exams associated with the product</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Exam search filter -->
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="examSearch" class="form-control" placeholder="Search exam...">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchQuizzes('exam')">
                                <i data-feather="search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- List of associated exams -->
                    <div id="examList">
                        @php
                            $examQuizzes = $productQuizzes->filter(function($quiz) {
                                return stripos($quiz->type->titre, 'exam') !== false;
                            });
                        @endphp
                        
                        @if($examQuizzes->count() > 0)
                            <div class="list-group">
                                @foreach($examQuizzes as $quiz)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $quiz->name_ar }}</h6>
                                            <small class="text-muted">{{ $quiz->name_en }}</small>
                                            <br>
                                            <span class="badge bg-light-warning">{{ $quiz->type->titre }}</span>
                                            <span class="badge bg-light-primary">{{ $quiz->score }}/100</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <form action="{{ route('products.quizzes.destroy', [$product, $quiz]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this exam?')">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i data-feather="file-text" class="text-muted" style="width: 48px; height: 48px;"></i>
                                <p class="text-muted mt-2">No exams associated</p>
                            </div>
                        @endif
                    </div>

                    <!-- Add an exam -->
                    <div class="mt-3">
                        <button class="btn btn-warning w-100" onclick="showAddQuizModal('exam')">
                            <i data-feather="plus" class="me-2"></i>
                            Add an Exam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal to add quizzes/exams -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-labelledby="addQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuizModalLabel">Add Quizzes/Exams</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addQuizForm" action="{{ route('products.quizzes.store', $product) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select id="quizTypeFilter" class="form-select" onchange="filterAvailableQuizzes()">
                            <option value="">All types</option>
                            @foreach($quizTypes as $type)
                                <option value="{{ $type->titre }}">{{ $type->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" id="modalSearch" class="form-control" placeholder="Search..." onkeyup="filterAvailableQuizzes()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Available Quizzes/Exams</label>
                        <div id="availableQuizzesList" style="max-height: 300px; overflow-y: auto;">
                            @foreach($availableQuizzes as $quiz)
                                <div class="form-check quiz-item" data-type="{{ $quiz->type->titre }}" data-name="{{ strtolower($quiz->name_ar . ' ' . $quiz->name_en) }}">
                                    <input class="form-check-input" type="checkbox" name="quiz_ids[]" value="{{ $quiz->id }}" id="quiz_{{ $quiz->id }}">
                                    <label class="form-check-label" for="quiz_{{ $quiz->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $quiz->name_ar }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $quiz->name_en }}</small>
                                            </div>
                                            <div>
                                                <span class="badge bg-light-info">{{ $quiz->type->titre }}</span>
                                                <span class="badge bg-light-primary">{{ $quiz->score }}/100</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addQuizForm" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSection = 'quiz';

function showAddQuizModal(section) {
    currentSection = section;
    const modal = new bootstrap.Modal(document.getElementById('addQuizModal'));
    modal.show();
    
    // Filter by type based on section
    const typeFilter = document.getElementById('quizTypeFilter');
    if (section === 'quiz') {
        typeFilter.value = 'Quiz';
    } else if (section === 'exam') {
        typeFilter.value = 'Examen';
    }
    filterAvailableQuizzes();
}

function filterAvailableQuizzes() {
    const searchTerm = document.getElementById('modalSearch').value.toLowerCase();
    const typeFilter = document.getElementById('quizTypeFilter').value;
    const quizItems = document.querySelectorAll('.quiz-item');
    
    quizItems.forEach(item => {
        const type = item.dataset.type;
        const name = item.dataset.name;
        const matchesSearch = name.includes(searchTerm);
        const matchesType = !typeFilter || type.includes(typeFilter);
        
        if (matchesSearch && matchesType) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function searchQuizzes(section) {
    const searchTerm = document.getElementById(section + 'Search').value;
    
    // You can implement an AJAX search here if needed
    // For now, we use client-side search
    console.log('Searching ' + section + ':', searchTerm);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
