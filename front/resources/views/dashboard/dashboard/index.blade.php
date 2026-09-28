@extends('dashboard.layouts.app')

@section('title', __('dashboard.dashboard'))

@section('content')
<div class="dashboard-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('dashboard.dashboard') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">{{ __('dashboard.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('dashboard.dashboard') }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>{{ __('dashboard.print_report') }}
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
                <i class="bi bi-plus-circle me-2"></i>{{ __('dashboard.quick_actions') }}
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="bi bi-play-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">{{ __('dashboard.enrolled_courses') }}</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['enrolled_courses'] ?? 12 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>+2 {{ __('dashboard.this_month') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="bi bi-check-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">{{ __('dashboard.completed_courses') }}</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['completed_courses'] ?? 8 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>+1 {{ __('dashboard.this_month') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="bi bi-award fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">{{ __('dashboard.certificates') }}</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['certificates'] ?? 5 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>+1 {{ __('dashboard.this_month') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="bi bi-clock fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title text-muted mb-1">{{ __('dashboard.study_hours') }}</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['study_hours'] ?? 156 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>+12 {{ __('dashboard.this_month') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Recent Courses -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('dashboard.recent_courses') }}</h5>
                        <a href="{{ route('dashboard.courses.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.course_name') }}</th>
                                    <th>{{ __('dashboard.progress') }}</th>
                                    <th>{{ __('dashboard.last_accessed') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/img/course-1.jpg') }}" alt="Course" class="rounded me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">{{ __('dashboard.python_basics') }}</h6>
                                                <small class="text-muted">{{ __('dashboard.programming') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 100px;">
                                            <div class="progress-bar" style="width: 75%"></div>
                                        </div>
                                        <small class="text-muted">75%</small>
                                    </td>
                                    <td>{{ __('dashboard.2_hours_ago') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">{{ __('dashboard.continue') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/img/course-2.jpg') }}" alt="Course" class="rounded me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">{{ __('dashboard.web_development') }}</h6>
                                                <small class="text-muted">{{ __('dashboard.programming') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 100px;">
                                            <div class="progress-bar" style="width: 45%"></div>
                                        </div>
                                        <small class="text-muted">45%</small>
                                    </td>
                                    <td>{{ __('dashboard.1_day_ago') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">{{ __('dashboard.continue') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/img/course-3.jpg') }}" alt="Course" class="rounded me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">{{ __('dashboard.data_science') }}</h6>
                                                <small class="text-muted">{{ __('dashboard.analytics') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 100px;">
                                            <div class="progress-bar" style="width: 90%"></div>
                                        </div>
                                        <small class="text-muted">90%</small>
                                    </td>
                                    <td>{{ __('dashboard.3_hours_ago') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">{{ __('dashboard.complete') }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">{{ __('dashboard.recent_activity') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="activity-icon bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ __('dashboard.course_completed') }}</h6>
                                    <p class="mb-1 text-muted">{{ __('dashboard.python_basics_completed') }}</p>
                                    <small class="text-muted">{{ __('dashboard.2_hours_ago') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="activity-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                        <i class="bi bi-play-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ __('dashboard.course_started') }}</h6>
                                    <p class="mb-1 text-muted">{{ __('dashboard.web_development_started') }}</p>
                                    <small class="text-muted">{{ __('dashboard.1_day_ago') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="activity-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                        <i class="bi bi-award"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ __('dashboard.certificate_earned') }}</h6>
                                    <p class="mb-1 text-muted">{{ __('dashboard.python_certificate') }}</p>
                                    <small class="text-muted">{{ __('dashboard.3_days_ago') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">{{ __('dashboard.quick_stats') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-primary">85%</h4>
                                <small class="text-muted">{{ __('dashboard.overall_progress') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-success">12</h4>
                                <small class="text-muted">{{ __('dashboard.streak_days') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-info">156</h4>
                                <small class="text-muted">{{ __('dashboard.total_hours') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-warning">5</h4>
                                <small class="text-muted">{{ __('dashboard.certificates') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('dashboard.quick_actions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('dashboard.courses.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="bi bi-play-circle fs-2 mb-2"></i>
                            {{ __('dashboard.enroll_course') }}
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('dashboard.certificates.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="bi bi-award fs-2 mb-2"></i>
                            {{ __('dashboard.view_certificates') }}
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="bi bi-person fs-2 mb-2"></i>
                            {{ __('dashboard.update_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
