@extends('layouts.app')

@section('title', 'Grade Practical Exam')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-clipboard-check"></i>
            </span>
            Grade Practical Exam
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('practical-exams.index') }}">Practical Exams</a></li>
                <li class="breadcrumb-item active">Grade</li>
            </ol>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Student & Course Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-account"></i> Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ trim(($attempt->user->first_name ?? '') . ' ' . ($attempt->user->last_name ?? '')) ?: 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $attempt->user->email ?? 'N/A' }}</p>
                            <p><strong>Course:</strong> {{ $attempt->product->titre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Attempt Number:</strong> #{{ $attempt->attempt_number }}</p>
                            <p><strong>Started:</strong>
                                @if($attempt->started_at)
                                    {{ $attempt->started_at->format('M d, Y H:i') }}
                                @else
                                    {{ $attempt->created_at->format('M d, Y H:i') }}
                                @endif
                            </p>
                            <p><strong>Submitted:</strong>
                                @if($attempt->submitted_at)
                                    {{ $attempt->submitted_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Case Details -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-book-open"></i> Training Case</h5>
                </div>
                <div class="card-body">
                    <h5>{{ $attempt->trainingCase->name ?? 'N/A' }}</h5>
                    <p class="text-muted mb-3">{{ $attempt->trainingCase->description ?? '' }}</p>

                    @if($attempt->trainingCaseFile)
                        <div class="alert alert-info">
                            <strong><i class="mdi mdi-file-document"></i> Assigned Case File</strong>
                            <div class="mt-2">
                                <a href="{{ route('training-case-files.download', $attempt->trainingCaseFile->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="mdi mdi-download"></i> Download {{ $attempt->trainingCaseFile->file_name }}
                                </a>
                                <small class="text-muted d-block mt-2">
                                    Size: {{ number_format($attempt->trainingCaseFile->file_size / 1024, 2) }} KB
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i> No case file assigned to this attempt
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Content -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-file-check"></i> Student Submission</h5>
                </div>
                <div class="card-body">
                    @if($attempt->video_url)
                        <div class="mb-3">
                            <span class="badge bg-info mb-2">Online Exam - Video Submission</span>
                            <div class="ratio ratio-16x9">
                                @if(strpos($attempt->video_url, 'youtube.com') !== false || strpos($attempt->video_url, 'youtu.be') !== false)
                                    @php
                                        $videoId = '';
                                        if (strpos($attempt->video_url, 'youtube.com/watch?v=') !== false) {
                                            parse_str(parse_url($attempt->video_url, PHP_URL_QUERY), $vars);
                                            $videoId = $vars['v'] ?? '';
                                        } elseif (strpos($attempt->video_url, 'youtu.be/') !== false) {
                                            $videoId = substr(parse_url($attempt->video_url, PHP_URL_PATH), 1);
                                        }
                                    @endphp
                                    @if($videoId)
                                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                                    @else
                                        <a href="{{ $attempt->video_url }}" target="_blank" class="btn btn-primary">
                                            <i class="mdi mdi-play-circle"></i> Open Video
                                        </a>
                                    @endif
                                @elseif(strpos($attempt->video_url, 'vimeo.com') !== false)
                                    @php
                                        $videoId = substr(parse_url($attempt->video_url, PHP_URL_PATH), 1);
                                    @endphp
                                    <iframe src="https://player.vimeo.com/video/{{ $videoId }}" allowfullscreen></iframe>
                                @else
                                    <a href="{{ $attempt->video_url }}" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="mdi mdi-play-circle"></i> Open Video Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <span class="badge bg-secondary mb-2">Classroom Exam</span>
                            <p class="mb-0"><i class="mdi mdi-information"></i> This exam was completed in a classroom setting. No video submission required.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Previous Feedback (if exists) -->
            @if($attempt->admin_comment && $attempt->status !== 'pending')
                <div class="card mb-4">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0"><i class="mdi mdi-comment-text"></i> Previous Feedback</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong>
                            @if($attempt->status === 'passed')
                                <span class="badge bg-success">Passed</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </p>
                        <p><strong>Reviewed by:</strong> {{ $attempt->reviewer->first_name ?? 'N/A' }} {{ $attempt->reviewer->last_name ?? '' }}</p>
                        <p><strong>Reviewed at:</strong> {{ $attempt->reviewed_at ? $attempt->reviewed_at->format('M d, Y H:i') : 'N/A' }}</p>
                        <hr>
                        <p class="mb-0">{{ $attempt->admin_comment }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grading Form -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient-danger text-white">
                    <h5 class="mb-0"><i class="mdi mdi-star"></i> Grade Submission</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('practical-exams.grade', $attempt->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Result <span class="text-danger">*</span></label>
                            <div class="d-grid gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_passed"
                                           value="passed" {{ old('status', $attempt->status) === 'passed' ? 'checked' : '' }} required>
                                    <label class="form-check-label text-success fw-bold" for="status_passed">
                                        <i class="mdi mdi-check-circle"></i> Pass
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_failed"
                                           value="failed" {{ old('status', $attempt->status) === 'failed' ? 'checked' : '' }} required>
                                    <label class="form-check-label text-danger fw-bold" for="status_failed">
                                        <i class="mdi mdi-close-circle"></i> Fail
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="admin_comment" class="form-label">Feedback <span class="text-danger">*</span></label>
                            <textarea name="admin_comment" id="admin_comment" class="form-control" rows="8"
                                      placeholder="Provide detailed feedback for the student..." required>{{ old('admin_comment', $attempt->admin_comment) }}</textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="alert alert-info small">
                            <i class="mdi mdi-information"></i> The student will receive an email notification with your feedback.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-gradient-primary btn-lg">
                                <i class="mdi mdi-content-save"></i> Submit Grade
                            </button>
                            <a href="{{ route('practical-exams.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Previous Attempts -->
            @php
                $previousAttempts = \App\Models\PracticalExamAttempt::where('user_id', $attempt->user_id)
                    ->where('product_id', $attempt->product_id)
                    ->where('id', '!=', $attempt->id)
                    ->orderBy('attempt_number', 'desc')
                    ->get();
            @endphp

            @if($previousAttempts->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-history"></i> Previous Attempts</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($previousAttempts as $prev)
                                <a href="{{ route('practical-exams.show', $prev->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><strong>Attempt #{{ $prev->attempt_number }}</strong></span>
                                        @if($prev->status === 'passed')
                                            <span class="badge bg-success">Passed</span>
                                        @elseif($prev->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($prev->status) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">{{ $prev->submitted_at ? $prev->submitted_at->format('M d, Y') : 'Not submitted' }}</small>
                                        <i class="mdi mdi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
}
</style>
@endsection
