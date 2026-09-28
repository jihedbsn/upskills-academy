@extends('layouts.dashboard')

@section('title', __('messages.practical_exam') . ' - ' . $product->titre)

@section('content')
<div class="dashboard__main">
    <div class="dashboard__content bg-light-4">
        <div class="row pb-50 mb-10">
            <div class="col-auto">
                <h1 class="text-30 lh-12 fw-700">{{ __('messages.practical_exam') }}</h1>
                <div class="mt-10">{{ $product->titre }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row y-gap-30">
            <!-- Main Content -->
            <div class="col-xl-9 col-lg-8">
                @if($hasPassed)
                    <!-- Passed Status -->
                    <div class="card border-success mb-30">
                        <div class="card-body text-center py-50">
                            <div class="icon-success mb-20">
                                <i class="icon-check-circle text-success" style="font-size: 64px;"></i>
                            </div>
                            <h3 class="text-24 fw-600 text-success mb-10">{{ __('messages.congratulations') }}</h3>
                            <p class="text-17">{{ __('messages.practical_exam_passed_message') }}</p>
                        </div>
                    </div>
                @elseif($currentAttempt)
                    @if($currentAttempt->status === 'pending')
                        <!-- Pending Review -->
                        <div class="card border-warning mb-30">
                            <div class="card-body text-center py-50">
                                <div class="icon-warning mb-20">
                                    <i class="icon-clock text-warning" style="font-size: 64px;"></i>
                                </div>
                                <h3 class="text-24 fw-600 text-warning mb-10">{{ __('messages.under_review') }}</h3>
                                <p class="text-17">{{ __('messages.exam_under_review_message') }}</p>
                                <p class="text-15 text-light-1">{{ __('messages.submitted_on') }}: {{ $currentAttempt->submitted_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- In Progress - Show Exam Interface -->
                        <div class="card mb-30">
                            <div class="card-header bg-dark-3">
                                <h4 class="text-18 fw-600 text-white">
                                    {{ __('messages.attempt') }} #{{ $currentAttempt->attempt_number }}
                                    <span class="badge bg-warning ms-10">{{ __('messages.in_progress') }}</span>
                                </h4>
                            </div>
                            <div class="card-body">
                                <!-- Training Case Info -->
                                <div class="alert alert-info">
                                    <h5 class="fw-600 mb-10">{{ $currentAttempt->trainingCase->name }}</h5>
                                    <p class="mb-0">{{ $currentAttempt->trainingCase->description }}</p>
                                </div>

                                <!-- Training Case Files -->
                                @if($currentAttempt->trainingCase->files->count() > 0)
                                    <div class="mb-30">
                                        <h5 class="text-17 fw-600 mb-15">{{ __('messages.training_materials') }}</h5>
                                        <div class="row g-3">
                                            @foreach($currentAttempt->trainingCase->files as $file)
                                                <div class="col-md-6">
                                                    <div class="card border">
                                                        <div class="card-body d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-15">
                                                                @if(in_array($file->file_extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <i class="icon-image text-primary" style="font-size: 32px;"></i>
                                                                @else
                                                                    <i class="icon-file-text text-danger" style="font-size: 32px;"></i>
                                                                @endif
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="fw-500">{{ $file->file_name }}</div>
                                                                <small class="text-light-1">{{ $file->formatted_size }}</small>
                                                            </div>
                                                            <a href="{{ $file->file_url }}" target="_blank" class="button -sm -dark-1 text-white">
                                                                {{ __('messages.view') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Download All Button for Classroom -->
                                        @if($product->practical_exam_type === 'classroom')
                                            <div class="mt-20">
                                                <a href="{{ route('student.practical-exam.download-case', $currentAttempt->id) }}"
                                                   class="button -md -purple-1 text-white">
                                                    <i class="icon-download mr-10"></i>
                                                    {{ __('messages.download_all_files') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Submission Form -->
                                @if($product->practical_exam_type === 'online')
                                    <!-- Online Exam - Video URL -->
                                    <form action="{{ route('student.practical-exam.submit', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-20">
                                            <label class="form-label fw-600">{{ __('messages.video_url') }} *</label>
                                            <input type="url"
                                                   name="video_url"
                                                   class="form-control @error('video_url') is-invalid @enderror"
                                                   placeholder="https://youtube.com/watch?v=... or https://drive.google.com/..."
                                                   required>
                                            @error('video_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-light-1 mt-5">
                                                {{ __('messages.video_url_help') }}
                                            </small>
                                        </div>

                                        <button type="submit" class="button -md -green-1 text-white" onclick="return confirm('{{ __('messages.confirm_submit_video') }}')">
                                            <i class="icon-upload mr-10"></i>
                                            {{ __('messages.submit_video') }}
                                        </button>
                                    </form>
                                @else
                                    <!-- Classroom Exam - Mark as Completed -->
                                    <div class="alert alert-warning">
                                        <i class="icon-info mr-10"></i>
                                        {{ __('messages.classroom_exam_instructions') }}
                                    </div>

                                    <form action="{{ route('student.practical-exam.mark-completed', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="button -md -green-1 text-white" onclick="return confirm('{{ __('messages.confirm_mark_completed') }}')">
                                            <i class="icon-check mr-10"></i>
                                            {{ __('messages.mark_as_completed') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Start Exam -->
                    <div class="card mb-30">
                        <div class="card-body text-center py-60">
                            <div class="mb-30">
                                <i class="icon-clipboard text-dark-1" style="font-size: 80px;"></i>
                            </div>
                            <h3 class="text-24 fw-600 mb-15">{{ __('messages.practical_exam') }}</h3>
                            <p class="text-17 mb-20">{{ __('messages.practical_exam_start_message') }}</p>

                            <div class="mb-30">
                                <div class="badge bg-purple-1 text-white px-20 py-10">
                                    <strong>{{ __('messages.exam_type') }}:</strong>
                                    {{ $product->practical_exam_type === 'online' ? __('messages.online_exam') : __('messages.classroom_exam') }}
                                </div>
                            </div>

                            <form action="{{ route('student.practical-exam.start', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="button -md -purple-1 text-white">
                                    <i class="icon-play mr-10"></i>
                                    {{ __('messages.start_practical_exam') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Attempt History -->
                @if($attempts->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-18 fw-600">{{ __('messages.attempt_history') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.attempt') }}</th>
                                            <th>{{ __('messages.training_case') }}</th>
                                            <th>{{ __('messages.submitted') }}</th>
                                            <th>{{ __('messages.status') }}</th>
                                            <th>{{ __('messages.feedback') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attempts as $attempt)
                                            <tr>
                                                <td><strong>#{{ $attempt->attempt_number }}</strong></td>
                                                <td>{{ $attempt->trainingCase->name }}</td>
                                                <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y') : '-' }}</td>
                                                <td>
                                                    @if($attempt->status === 'passed')
                                                        <span class="badge bg-success">{{ __('messages.passed') }}</span>
                                                    @elseif($attempt->status === 'failed')
                                                        <span class="badge bg-danger">{{ __('messages.failed') }}</span>
                                                    @elseif($attempt->status === 'pending')
                                                        <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('messages.in_progress') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt->admin_comment)
                                                        <button type="button" class="button -sm -dark-1 text-white"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#feedbackModal{{ $attempt->id }}">
                                                            {{ __('messages.view_feedback') }}
                                                        </button>

                                                        <!-- Feedback Modal -->
                                                        <div class="modal fade" id="feedbackModal{{ $attempt->id }}" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">{{ __('messages.instructor_feedback') }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>{{ $attempt->admin_comment }}</p>
                                                                        @if($attempt->reviewer)
                                                                            <hr>
                                                                            <small class="text-light-1">
                                                                                {{ __('messages.reviewed_by') }}: {{ $attempt->reviewer->name }}
                                                                                <br>
                                                                                {{ __('messages.reviewed_on') }}: {{ $attempt->reviewed_at->format('M d, Y H:i') }}
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-light-1">{{ __('messages.pending_review') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-17 fw-600">{{ __('messages.exam_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-20">
                            <div class="text-15 fw-500 mb-5">{{ __('messages.exam_type') }}</div>
                            <div class="text-14 text-light-1">
                                {{ $product->practical_exam_type === 'online' ? __('messages.online_video_submission') : __('messages.physical_exam_at_facility') }}
                            </div>
                        </div>

                        <div class="mb-20">
                            <div class="text-15 fw-500 mb-5">{{ __('messages.total_attempts') }}</div>
                            <div class="text-14 text-light-1">{{ $attempts->count() }}</div>
                        </div>

                        @if($hasPassed)
                            <div class="alert alert-success py-10">
                                <i class="icon-check-circle mr-10"></i>
                                <strong>{{ __('messages.completed') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
