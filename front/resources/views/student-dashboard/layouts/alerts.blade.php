{{-- Pending Evaluations Alert --}}
@if(isset($pendingEvaluationsCount) && $pendingEvaluationsCount > 0)
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="bi bi-star fs-4 me-3"></i>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-1">
                {{ trans_choice('student_dashboard.courses_awaiting_evaluation', $pendingEvaluationsCount, ['count' => $pendingEvaluationsCount]) }}
            </h6>
            <p class="mb-0 small">
                {{ __('student_dashboard.please_rate_feedback') }}
            </p>
        </div>
        @if(isset($pendingEvaluations) && $pendingEvaluations->count() > 0)
            @php
                $firstPendingCourse = $pendingEvaluations->first();
            @endphp
            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $firstPendingCourse->id]) }}#rate-course" class="btn btn-sm btn-warning ms-3">
                {{ __('student_dashboard.view_course') }}
            </a>
        @else
            <a href="{{ route('courses.index', ['locale' => app()->getLocale()]) }}" class="btn btn-sm btn-warning ms-3">
                {{ __('student_dashboard.view_courses') }}
            </a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
