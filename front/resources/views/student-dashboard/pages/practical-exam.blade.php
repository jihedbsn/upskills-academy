@extends('student-dashboard.layouts.app')

@section('title', __('messages.practical_exam') . ' - ' . $product->titre)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.courses.index') }}">{{ __('student_dashboard.my_courses') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.practical_exam') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.practical_exam') }}</h1>
            <p class="text-muted">{{ $product->titre }}</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @if($hasPassed)
                <!-- Passed Status -->
                <div class="card border-success mb-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-success mb-3">{{ __('messages.congratulations') }}</h3>
                        <p class="lead text-muted">{{ __('messages.practical_exam_passed_message') }}</p>
                    </div>
                </div>
            @elseif($currentAttempt)
                @if($currentAttempt->status === 'pending_review')
                    <!-- Pending Review -->
                    <div class="card border-warning mb-4">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-clock-history text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-warning mb-3">{{ __('messages.under_review') }}</h3>
                            <p class="lead text-muted">{{ __('messages.exam_under_review_message') }}</p>
                            @if($currentAttempt->submitted_at)
                                <p class="text-muted mt-3">
                                    <small>{{ __('messages.submitted_on') }}: {{ $currentAttempt->submitted_at->format('M d, Y H:i') }}</small>
                                </p>
                            @endif
                        </div>
                    </div>
                @elseif($currentAttempt->status === 'pending_submission')
                    <!-- In Progress -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 text-white">{{ __('messages.attempt') }} #{{ $currentAttempt->attempt_number }}</h5>
                                <small class="opacity-75">{{ $currentAttempt->trainingCase->name }}</small>
                            </div>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i> {{ __('messages.in_progress') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-book" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title">{{ __('messages.training_case') }}</h5>
                                    <p class="card-text">{{ $currentAttempt->trainingCase->description }}</p>

                                    @if(!$currentAttempt->case_downloaded)
                                        <!-- Download Case Button -->
                                        <div class="alert alert-info mb-0 d-flex align-items-center">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span class="me-auto">{{ __('messages.download_case_first') }}</span>
                                            <a href="{{ route('student.practical-exam.download-case', ['locale' => app()->getLocale(), 'attempt' => $currentAttempt->id]) }}"
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-download me-1"></i>{{ __('messages.download_case') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($currentAttempt->case_downloaded)
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submissionModal">
                                        <i class="bi bi-upload me-2"></i>{{ __('messages.submit_video') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <!-- Start Exam -->
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-clipboard-check text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="mb-3">{{ __('messages.practical_exam') }}</h3>
                        <p class="text-muted mb-4">{{ __('messages.practical_exam_start_message') }}</p>

                        <div class="mb-4">
                            <span class="text-muted d-block mb-1">{{ __('messages.exam_type') }}</span>
                            <span class="h5">
                                {{ $product->practical_exam_type === 'online' ? __('messages.online_exam') : __('messages.classroom_exam') }}
                            </span>
                        </div>

                        <form action="{{ route('student.practical-exam.start', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-play-fill me-2"></i>{{ __('messages.start_practical_exam') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Attempt History -->
            @if($attempts->count() > 0)
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('messages.attempt_history') }}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.attempt') }}</th>
                                    <th>{{ __('messages.training_case') }}</th>
                                    <th>{{ __('messages.submitted') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.feedback') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td><span class="fw-bold">#{{ $attempt->attempt_number }}</span></td>
                                        <td>{{ $attempt->trainingCaseFile ? $attempt->trainingCaseFile->file_name : $attempt->trainingCase->name }}</td>
                                        <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y') : '-' }}</td>
                                        <td>
                                            @if($attempt->status === 'passed')
                                                <span class="badge bg-success">{{ __('messages.passed') }}</span>
                                            @elseif($attempt->status === 'failed')
                                                <span class="badge bg-danger">{{ __('messages.failed') }}</span>
                                            @elseif($attempt->status === 'pending_review')
                                                <span class="badge bg-warning text-dark">{{ __('messages.pending_review') }}</span>
                                            @elseif($attempt->status === 'pending_submission')
                                                <span class="badge bg-info text-dark">{{ __('messages.pending_submission') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->admin_comment)
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $attempt->id }}">
                                                    <i class="bi bi-chat-text me-1"></i>{{ __('messages.view_feedback') }}
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('student.practical-exam.download-case', ['locale' => app()->getLocale(), 'attempt' => $attempt->id]) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download me-1"></i>{{ __('messages.download_case') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Submission Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.video_submission') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('student.practical-exam.submit', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-light border d-flex align-items-center mb-3" role="alert">
                            <i class="bi bi-info-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <small>{{ __('messages.video_url_submission_instructions') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.video_url') }} <span class="text-danger">*</span></label>
                            <input type="url"
                                   name="video_url"
                                   class="form-control @error('video_url') is-invalid @enderror"
                                   placeholder="https://youtube.com/watch?v=... or https://drive.google.com/..."
                                   required>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-link-45deg"></i> {{ __('messages.video_url_help') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('messages.confirm_submit_video') }}')">
                            <i class="bi bi-upload me-1"></i> {{ __('messages.submit_video') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Feedback Modals -->
    @foreach($attempts as $attempt)
        @if($attempt->admin_comment)
            <div class="modal fade" id="feedbackModal{{ $attempt->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">
                                <i class="bi bi-chat-quote me-2"></i>{{ __('messages.instructor_feedback') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-3 bg-light rounded mb-3">
                                <p class="mb-0">{{ $attempt->admin_comment }}</p>
                            </div>

                            @if($attempt->reviewer)
                                <div class="d-flex justify-content-between text-muted small border-top pt-3">
                                    <div>
                                        <i class="bi bi-person me-1"></i>
                                        <strong>{{ __('messages.reviewed_by') }}:</strong>
                                        {{ $attempt->reviewer->nom ?? 'N/A' }} {{ $attempt->reviewer->prenom ?? '' }}
                                    </div>
                                    <div>
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $attempt->reviewed_at ? $attempt->reviewed_at->format('M d, Y H:i') : 'N/A' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection
