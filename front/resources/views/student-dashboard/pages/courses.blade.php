@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.my_courses'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.my_courses') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.my_courses') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.enrolled_courses') }}</p>
        </div>
        <div>
            <a href="{{ route('student-dashboard.history.index') }}" class="btn btn-outline-primary w-100 w-md-auto">
                <i class="bi bi-clock-history me-2"></i>{{ __('student_dashboard.quiz_exam_history') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $courseStats['total_enrolled'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.enrolled_courses') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $courseStats['completed'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.completed_courses') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $courseStats['pending'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.pending_validation') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $courseStats['in_progress'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.in_progress') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Courses Grid - Udemy Style -->
    <div class="row">
        @forelse($enrolledProductStudents as $enrolledProductStudent)
            @php
                // Vérifier que l'objet et ses relations existent
                if (!$enrolledProductStudent || !$enrolledProductStudent->product) {
                    continue;
                }

                $course = $enrolledProductStudent->product;
                $courseProgress = Auth::user()->getProductProgress($course->id);

                // Vérifier le statut StudentSuccess
                $studentSuccess = $studentSuccesses->where('product_id', $course->id)->first();
                $successStatus = null; // null = pas encore de StudentSuccess
                $isCompleted = false;

                if ($studentSuccess) {
                    $successStatus = $studentSuccess->success; // 0 = en attente, 1 = succès, -1 = refusé
                    $isCompleted = ($successStatus == 1);
                }

                $hasExamQuiz = $course->quizzes()->where('type_id', 1)->exists(); // Type 1 = Exam
                $examResult = null;
                if ($hasExamQuiz) {
                    $examQuiz = $course->quizzes()->where('type_id', 1)->first();
                    $examResult = Auth::user()->getQuizResult($course->id, $examQuiz->id);
                }

                // Get or calculate rating
                $courseRating = $course->ratings()->where('student_id', Auth::id())->first();
                $averageRating = $course->ratings()->avg('rating') ?? 0;
                $totalRatings = $course->ratings()->count();
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card udemy-course-card h-100 border-0 shadow-sm">
                    <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $course->id]) }}" class="text-decoration-none">
                        <div class="position-relative course-thumbnail">
                            <img src="{{ $course->image ? config('app.file_url') . 'uploads/products/images/' . $course->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                 class="card-img-top"
                                 alt="{{ $course->titre }}"
                                 onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                            @if($successStatus === 0)
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-warning px-2 py-1">
                                        <i class="bi bi-clock me-1"></i>{{ __('student_dashboard.pending_validation') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>

                    <div class="card-body p-3">
                        <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $course->id]) }}" class="text-decoration-none text-dark">
                            <h5 class="card-title course-title mb-2">{{ $course->titre }}</h5>
                        </a>

                        <!-- Instructor Info -->
                        @if($course->teacher)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person-circle me-1"></i>{{ $course->teacher->nom ?? 'Instructor' }}
                            </p>
                        @endif

                        <!-- Rating Display -->
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold text-warning me-1">{{ number_format($averageRating, 1) }}</span>
                            <div class="stars-display me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @elseif($i - $averageRating < 1)
                                        <i class="bi bi-star-half text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-muted small">({{ $totalRatings }})</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark">{{ $courseProgress['progress_percentage'] }}% {{ __('student_dashboard.complete') }}</span>
                                @if($successStatus === 1)
                                    @php
                                        $certifStudent = $studentSuccess ? $studentSuccess->certificates()->first() : null;
                                    @endphp
                                    @if($certifStudent && $certifStudent->file_path)
                                        <a href="{{ $certifStudent->file_url }}" target="_blank" class="badge-with-certificate" download>
                                            <i class="bi bi-trophy-fill"></i>
                                            <span>{{ __('student_dashboard.completed') }}</span>
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('student-dashboard.certificates.show', $studentSuccess->id) }}" class="badge-with-certificate">
                                            <i class="bi bi-trophy-fill"></i>
                                            <span>{{ __('student_dashboard.completed') }}</span>
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <div class="progress" style="height: 5px; border-radius: 10px;">
                                <div class="progress-bar @if($isCompleted) bg-success @else bg-primary @endif"
                                     role="progressbar"
                                     style="width: {{ $courseProgress['progress_percentage'] }}%"
                                     aria-valuenow="{{ $courseProgress['progress_percentage'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Your Rating -->
                        @if($courseRating)
                            <div class="your-rating mb-2">
                                <small class="text-muted d-block mb-1">{{ __('student_dashboard.your_rating') }}:</small>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $courseRating->rating)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 small text-muted">({{ $courseRating->rating }}/5)</span>
                                </div>
                            </div>
                        @endif

                        <!-- Course Info -->
                        <div class="course-meta d-flex justify-content-between text-muted small mb-2">
                            <span>
                                <i class="bi bi-play-circle me-1"></i>{{ $courseProgress['total_quizzes'] }} {{ __('student_dashboard.quizzes') }}
                            </span>
                            <span>
                                <i class="bi bi-calendar-event me-1"></i>{{ $enrolledProductStudent && $enrolledProductStudent->created_at ? $enrolledProductStudent->created_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>

                        @if($hasExamQuiz && $examResult)
                            <div class="exam-result-badge">
                                @if($examResult->success)
                                    <span class="badge bg-success-subtle text-success w-100 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i>{{ __('student_dashboard.exam_passed') }}: {{ $examResult->score }}%
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger w-100 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i>{{ __('student_dashboard.exam_failed') }}: {{ $examResult->score }}%
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Course Extension Badge --}}
                        @if($enrolledProductStudent->is_expired || ($enrolledProductStudent->expiration_date && $enrolledProductStudent->expiration_date->isPast()))
                            <div class="mt-2">
                                <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $course->id]) }}"
                                   class="btn btn-danger btn-sm w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>{{ __('student_dashboard.ext_renew_access') }}
                                </a>
                            </div>
                        @elseif($enrolledProductStudent->expiration_date && $enrolledProductStudent->daysRemaining() <= 30 && $enrolledProductStudent->daysRemaining() > 0)
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark w-100 py-1 mb-1">
                                    <i class="bi bi-clock me-1"></i>{{ __('student_dashboard.ext_expiring_in_days', ['days' => $enrolledProductStudent->daysRemaining()]) }}
                                </span>
                                <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $course->id]) }}"
                                   class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>{{ __('student_dashboard.ext_extend_access') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">{{ __('student_dashboard.no_courses_enrolled') }}</h4>
                    <p class="text-muted">{{ __('student_dashboard.enroll_your_first_course') }}</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-search me-2"></i>{{ __('messages.browse_courses') ?? 'Browse Courses' }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>

<!-- Course Details Modal -->
<div class="modal fade" id="courseDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.course_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5>{{ __('student_dashboard.python_basics') }}</h5>
                        <p class="text-muted">{{ __('student_dashboard.learn_python_fundamentals') }}</p>

                        <div class="mb-3">
                            <h6>{{ __('student_dashboard.course_content') }}</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success me-2"></i>{{ __('student_dashboard.variables_and_types') }}</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>{{ __('student_dashboard.control_structures') }}</li>
                                <li><i class="bi bi-circle text-muted me-2"></i>{{ __('student_dashboard.functions_and_modules') }}</li>
                                <li><i class="bi bi-circle text-muted me-2"></i>{{ __('student_dashboard.object_oriented_programming') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>{{ __('student_dashboard.course_info') }}</h6>
                                <ul class="list-unstyled">
                                    <li><strong>{{ __('student_dashboard.duration') }}:</strong> 12 {{ __('student_dashboard.hours') }}</li>
                                    <li><strong>{{ __('student_dashboard.lessons') }}:</strong> 24</li>
                                    <li><strong>{{ __('student_dashboard.quizzes') }}:</strong> 8</li>
                                    <li><strong>{{ __('student_dashboard.certificate') }}:</strong> {{ __('student_dashboard.yes') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.close') }}</button>
                <a href="#" class="btn btn-primary">{{ __('student_dashboard.continue_learning') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
