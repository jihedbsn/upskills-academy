@extends('student-dashboard.layouts.app')

@section('title', __('messages.practical_exams_history'))

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.practical_exams_history') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.practical_exams_history') }}</h1>
            <p class="text-muted">{{ __('messages.view_all_your_practical_exam_attempts') }}</p>
        </div>
    </div>

    @if($attempts->isEmpty())
        <div class="card text-center py-5">
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="card-title">{{ __('messages.no_practical_exams_yet') }}</h4>
                <p class="card-text text-muted">{{ __('messages.start_practical_exam_from_course') }}</p>
                <a href="{{ route('student-dashboard.courses.index') }}" class="btn btn-primary mt-3">
                    {{ __('student_dashboard.my_courses') }}
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">{{ __('messages.course') }}</th>
                            <th class="py-3">{{ __('messages.exam_type') }}</th>
                            <th class="py-3">{{ __('messages.attempt') }}</th>
                            <th class="py-3">{{ __('messages.status') }}</th>
                            <th class="py-3">{{ __('messages.submitted_at') }}</th>
                            <th class="py-3">{{ __('messages.reviewed_at') }}</th>
                            <th class="py-3 text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr>
                                <!-- Course Info -->
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $attempt->product->titre ?? $attempt->product->video ?? __('messages.course') }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $attempt->trainingCase ? ($attempt->trainingCase->title ?? $attempt->trainingCase->name) : __('messages.training_case') }}
                                    </div>
                                </td>

                                <!-- Exam Type -->
                                <td>
                                    @if($attempt->product->practical_exam_type === 'online')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 rounded-pill px-3 py-2">
                                            <i class="bi bi-laptop me-1"></i> {{ __('messages.online') }}
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3 py-2">
                                            <i class="bi bi-building me-1"></i> {{ __('messages.classroom') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Attempt Number -->
                                <td>
                                    <span class="fw-bold">#{{ $attempt->attempt_number }}</span>
                                </td>

                                <!-- Status -->
                                <td>
                                    @if($attempt->status === 'passed')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> {{ __('messages.passed') }}
                                        </span>
                                    @elseif($attempt->status === 'failed')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> {{ __('messages.failed') }}
                                        </span>
                                    @elseif($attempt->status === 'pending_review')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i> {{ __('messages.pending_review') }}
                                        </span>
                                    @elseif($attempt->status === 'pending_submission')
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-pencil me-1"></i> {{ __('messages.pending_submission') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Submitted At -->
                                <td>
                                    @if($attempt->submitted_at)
                                        <div class="fw-medium">{{ $attempt->submitted_at->format('M d, Y') }}</div>
                                        <div class="small text-muted">{{ $attempt->submitted_at->format('h:i A') }}</div>
                                    @else
                                        <span class="text-muted small">{{ __('messages.not_submitted') }}</span>
                                    @endif
                                </td>

                                <!-- Reviewed At -->
                                <td>
                                    @if($attempt->reviewed_at)
                                        <div class="fw-medium">{{ $attempt->reviewed_at->format('M d, Y') }}</div>
                                        <div class="small text-muted">{{ $attempt->reviewed_at->format('h:i A') }}</div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="text-end">
                                    <a href="{{ route('student.practical-exam.show', ['locale' => app()->getLocale(), 'product' => $attempt->product_id]) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye me-1"></i> {{ __('messages.view_details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
