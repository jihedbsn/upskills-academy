@extends('layouts.app')

@section('title', 'Student Details - ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="user" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">{{ $student->full_name }}</h1>
                                <p class="text-muted mb-0">Student Profile & Academic Progress</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit Profile
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Student Profile Card -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-feather="user" class="me-2"></i>
                                Profile Information
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <!-- Profile Photo -->
                            <div class="mb-4">
                                @if($student->image)
                                    <img src="{{ $student->image_url }}" 
                                         alt="{{ $student->full_name }}" 
                                         class="rounded-circle shadow-sm mb-3"
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="avatar bg-light text-dark rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                         style="width: 120px; height: 120px;">
                                        <i data-feather="user" style="width: 48px; height: 48px;"></i>
                                    </div>
                                @endif
                                <h4 class="mb-1">{{ $student->full_name }}</h4>
                                <p class="text-muted mb-0">Student ID: #{{ $student->id }}</p>
                            </div>

                            <!-- Contact Information -->
                            <div class="text-start">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">EMAIL ADDRESS</label>
                                    <div class="d-flex align-items-center">
                                        <i data-feather="mail" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
                                        <a href="mailto:{{ $student->email }}" class="text-decoration-none">
                                            {{ $student->email }}
                                        </a>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">PHONE NUMBER</label>
                                    <div class="d-flex align-items-center">
                                        <i data-feather="phone" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
                                        @if($student->phone)
                                            <a href="tel:{{ $student->phone }}" class="text-decoration-none">
                                                {{ $student->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">COUNTRY</label>
                                    <div class="d-flex align-items-center">
                                        <i data-feather="globe" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
                                        @if($student->country)
                                            <span class="badge bg-info">{{ $student->country }}</span>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">EMAIL STATUS</label>
                                    <div class="d-flex align-items-center">
                                        @if($student->email_verified_at)
                                            <span class="badge bg-success">
                                                <i data-feather="check" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Verified
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i data-feather="alert-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Not Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">REGISTRATION DATE</label>
                                    <div class="d-flex align-items-center">
                                        <i data-feather="calendar" class="me-2 text-primary" style="width: 16px; height: 16px;"></i>
                                        <span>{{ $student->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <small class="text-muted">{{ $student->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-feather="zap" class="me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="mailto:{{ $student->email }}" class="btn btn-outline-primary">
                                    <i data-feather="mail" class="me-2"></i>
                                    Send Email
                                </a>
                                @if($student->phone)
                                <a href="tel:{{ $student->phone }}" class="btn btn-outline-success">
                                    <i data-feather="phone" class="me-2"></i>
                                    Call Student
                                </a>
                                @endif
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="col-lg-8">
                    <!-- Statistics Overview -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i data-feather="book-open" class="mb-2" style="width: 32px; height: 32px;"></i>
                                    <h3 class="mb-1">{{ $enrolledCourses->count() }}</h3>
                                    <p class="mb-0 small">Enrolled Courses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i data-feather="award" class="mb-2" style="width: 32px; height: 32px;"></i>
                                    <h3 class="mb-1">{{ $quizStats['successful_attempts'] }}</h3>
                                    <p class="mb-0 small">Quiz Passed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i data-feather="file-text" class="mb-2" style="width: 32px; height: 32px;"></i>
                                    <h3 class="mb-1">{{ $stageStats['validated'] }}</h3>
                                    <p class="mb-0 small">Stages Validated</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i data-feather="video" class="mb-2" style="width: 32px; height: 32px;"></i>
                                    <h3 class="mb-1">{{ $videoExamStats['validated'] }}</h3>
                                    <p class="mb-0 small">Video Exams Passed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Courses -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i data-feather="book-open" class="me-2"></i>
                                    Enrolled Courses ({{ $enrolledCourses->count() }})
                                </h5>
                                @if($enrolledCourses->count() > 0)
                                <span class="badge bg-primary">${{ $enrolledCourses->sum('price') }} Total Paid</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($enrolledCourses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Course</th>
                                                <th>Price Paid</th>
                                                <th>Enrollment Date</th>
                                                <th>Payment Method</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($enrolledCourses as $order)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                                <i data-feather="book" class="text-white" style="width: 16px; height: 16px;"></i>
                                                            </div>
                                                            <div>
                                                                <strong>
                                                                    {{ $order->product->name_en ?? $order->product->name_ar ?? 'Course #' . $order->product->id }}
                                                                </strong>
                                                                <br>
                                                                <small class="text-muted">ID: {{ $order->product->id }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($order->price > 0)
                                                            <span class="badge bg-success">${{ number_format($order->price, 2) }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Free</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">{{ $order->created_at->format('M d, Y') }}</span>
                                                        <br>
                                                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst($order->payment_method ?? 'Not specified') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('products.show', $order->product) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           target="_blank"
                                                           title="View Course Details">
                                                            <i data-feather="external-link" style="width: 14px; height: 14px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="book-open" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                                    <h6 class="text-muted">No Enrolled Courses</h6>
                                    <p class="text-muted mb-0">This student hasn't enrolled in any courses yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Performance -->
                    <div class="row">
                        <!-- Quiz Performance -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i data-feather="help-circle" class="me-2"></i>
                                        Quiz Performance
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($quizStats['total_attempts'] > 0)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Total Attempts</span>
                                                <strong>{{ $quizStats['total_attempts'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Success Rate</span>
                                                <strong class="text-success">
                                                    {{ $quizStats['total_attempts'] > 0 ? round(($quizStats['successful_attempts'] / $quizStats['total_attempts']) * 100, 1) : 0 }}%
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Average Score</span>
                                                <strong>{{ $quizStats['average_score'] }}/100</strong>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Best Score</span>
                                                <strong class="text-primary">{{ $quizStats['best_score'] }}/100</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i data-feather="help-circle" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                                            <p class="text-muted mb-0 small">No quiz attempts yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Stage Submissions -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i data-feather="file-text" class="me-2"></i>
                                        Stage Submissions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($stageStats['total_submissions'] > 0)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Total Submissions</span>
                                                <strong>{{ $stageStats['total_submissions'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Validated</span>
                                                <strong class="text-success">{{ $stageStats['validated'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Rejected</span>
                                                <strong class="text-danger">{{ $stageStats['rejected'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Pending</span>
                                                <strong class="text-warning">{{ $stageStats['pending'] }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i data-feather="file-text" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                                            <p class="text-muted mb-0 small">No stage submissions yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Video Exam Submissions -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i data-feather="video" class="me-2"></i>
                                        Video Exams
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($videoExamStats['total_submissions'] > 0)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Total Submissions</span>
                                                <strong>{{ $videoExamStats['total_submissions'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Validated</span>
                                                <strong class="text-success">{{ $videoExamStats['validated'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Rejected</span>
                                                <strong class="text-danger">{{ $videoExamStats['rejected'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Pending</span>
                                                <strong class="text-warning">{{ $videoExamStats['pending'] }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i data-feather="video" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                                            <p class="text-muted mb-0 small">No video exam submissions yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.badge {
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    color: #5a5c69;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}

@media (max-width: 768px) {
    .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
        border-radius: 0.25rem !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();
    
    // Auto-refresh icons after any dynamic content changes
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
