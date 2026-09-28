@extends('student-dashboard.layouts.app')

@section('title', __('messages.course_schedule'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('messages.course_schedule') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.course_schedule') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.view_all_scheduled_sessions') }}</p>
        </div>
    </div>

    @if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
        <!-- Upcoming Sessions -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2" style="color: #0071ae;"></i>{{ __('messages.upcoming_sessions') }}</h5>
                    <span class="badge rounded-pill" style="background-color: #0071ae;">{{ $upcomingSessions->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($upcomingSessions as $session)
                    <div class="session-card border-bottom p-4 hover-bg-light">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Title and Type -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0 fw-semibold">{{ $session->title }}</h5>
                                    @if($session->session_type === 'online')
                                        <span class="badge bg-success rounded-pill">
                                            <i class="bi bi-camera-video me-1"></i>{{ __('messages.online') }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill" style="background-color: #0071ae;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ __('messages.in_person') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Course Name -->
                                @if($session->product)
                                    <p class="mb-3">
                                        <i class="bi bi-book me-2" style="color: #0071ae;"></i>
                                        <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $session->product->id]) }}" class="text-decoration-none fw-medium" style="color: #0071ae;">
                                            {{ $session->product->titre }}
                                        </a>
                                    </p>
                                @endif

                                <!-- Description -->
                                @if($session->description)
                                    <p class="text-muted mb-3">{{ $session->description }}</p>
                                @endif

                                <!-- Session Details Grid -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box me-3" style="background-color: rgba(0, 113, 174, 0.1); color: #0071ae;">
                                                <i class="bi bi-calendar3"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">{{ __('messages.date') }}</small>
                                                <strong>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                                <i class="bi bi-clock"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">{{ __('messages.time') }}</small>
                                                <strong>
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }} -
                                                    {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    @if($session->instructor_name)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">{{ __('messages.instructor') }}</small>
                                                    <strong>{{ $session->instructor_name }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($session->location)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box me-3" style="background-color: rgba(0, 113, 174, 0.1); color: #0071ae;">
                                                    <i class="bi bi-geo-alt"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">{{ __('messages.location') }}</small>
                                                    <strong>{{ $session->location }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Notes -->
                                @if($session->notes)
                                    <div class="alert mt-3 mb-0 d-flex align-items-start" style="background-color: rgba(0, 113, 174, 0.1); border-color: #0071ae; color: #0071ae;">
                                        <i class="bi bi-info-circle me-2 mt-1" style="color: #0071ae;"></i>
                                        <div>
                                            <strong>{{ __('messages.important_notes') }}:</strong>
                                            <p class="mb-0 mt-1">{{ $session->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Column -->
                            <div class="col-lg-4 d-flex align-items-center justify-content-center border-start mt-4 mt-lg-0">
                                @if($session->session_type === 'online' && $session->zoom_join_url)
                                    <div class="text-center w-100 px-3">
                                        <div class="mb-3">
                                            <i class="bi bi-camera-video text-success" style="font-size: 3rem;"></i>
                                        </div>
                                        <a href="{{ $session->zoom_join_url }}" target="_blank" class="btn btn-success w-100 mb-3">
                                            <i class="bi bi-box-arrow-up-right me-2"></i>{{ __('messages.join_meeting') }}
                                        </a>
                                        @if($session->zoom_meeting_id)
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted d-block mb-1">{{ __('messages.meeting_id') }}</small>
                                                <code class="text-dark fs-6">{{ $session->zoom_meeting_id }}</code>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center px-3">
                                        <i class="bi bi-geo-alt" style="font-size: 3.5rem; color: #0071ae;"></i>
                                        @if($session->location)
                                            <p class="mb-0 mt-3 fw-semibold">{{ $session->location }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($pastSessions) && $pastSessions->count() > 0)
        <!-- Past Sessions -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>{{ __('messages.past_sessions') }}</h5>
                    <span class="badge bg-secondary rounded-pill">{{ $pastSessions->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($pastSessions as $session)
                    <div class="past-session-card border-bottom p-3 hover-bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="mb-2 fw-semibold">{{ $session->title }}</h6>
                                @if($session->product)
                                    <p class="mb-2 text-muted">
                                        <small>
                                            <i class="bi bi-book me-1"></i>
                                            {{ $session->product->titre }}
                                        </small>
                                    </p>
                                @endif
                                <p class="mb-0">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                    </small>
                                </p>
                            </div>
                            <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                <span class="badge bg-secondary rounded-pill me-1">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('messages.completed') }}
                                </span>
                                @if($session->session_type === 'online')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                        <i class="bi bi-camera-video me-1"></i>{{ __('messages.online') }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background-color: rgba(0, 113, 174, 0.15); color: #0071ae;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ __('messages.in_person') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if((!isset($upcomingSessions) || $upcomingSessions->count() == 0) && (!isset($pastSessions) || $pastSessions->count() == 0))
        <!-- Empty State -->
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                </div>
                <h4 class="mb-2">{{ __('messages.no_sessions_scheduled') }}</h4>
                <p class="text-muted mb-4">{{ __('messages.sessions_will_appear_here') }}</p>
                <a href="{{ route('student-dashboard.courses.index') }}" class="btn" style="background-color: #0071ae; border-color: #0071ae; color: white;">
                    <i class="bi bi-book me-2"></i>{{ __('messages.browse_my_courses') }}
                </a>
            </div>
        </div>
    @endif
</div>

<style>
/* Course Schedule Custom Styles */
.session-card {
    transition: all 0.3s ease;
}

.session-card:hover {
    background-color: #f8f9fa;
}

.past-session-card {
    transition: all 0.2s ease;
}

.past-session-card:hover {
    background-color: #f8f9fa;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

.icon-box {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.85em;
}

.session-card:last-child {
    border-bottom: none !important;
}

.past-session-card:last-child {
    border-bottom: none !important;
}

/* Custom primary color */
.text-primary, .bi-calendar-check.text-primary, .bi-book.text-primary,
.icon-box.text-primary, .bi-geo-alt.text-primary {
    color: #0071ae !important;
}

.bg-primary {
    background-color: #0071ae !important;
}

.icon-box.bg-primary {
    background-color: rgba(0, 113, 174, 0.1) !important;
}

.btn-primary {
    background-color: #0071ae !important;
    border-color: #0071ae !important;
}

.btn-primary:hover, .btn-primary:focus {
    background-color: #005a8c !important;
    border-color: #005a8c !important;
}

.badge.bg-primary {
    background-color: #0071ae !important;
}

a.text-primary:hover {
    color: #005a8c !important;
}

@media (max-width: 991px) {
    .border-start {
        border-left: none !important;
        border-top: 1px solid #dee2e6 !important;
        padding-top: 1.5rem !important;
    }
}
</style>
@endsection
