@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.ext_course_extensions'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.ext_course_extensions') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.ext_course_extensions') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.ext_description') }}</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Expired Courses Section --}}
    @if($expiredCourses->count() > 0)
    <div class="mb-4">
        <h5 class="text-danger mb-3">
            <i class="bi bi-exclamation-circle me-2"></i>{{ __('student_dashboard.ext_expired_courses') }}
        </h5>
        <div class="row">
            @foreach($expiredCourses as $ps)
                @if($ps->product)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                    <div class="card border-danger h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <img src="{{ $ps->product->image ? config('app.file_url') . 'uploads/products/images/' . $ps->product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     class="rounded me-3" width="60" height="60" style="object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $ps->product->titre }}</h6>
                                    @if($ps->product->teacher)
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ps->product->teacher->nom ?? '' }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">{{ __('student_dashboard.ext_expired_on') }}:</span>
                                    <span class="text-danger fw-bold">{{ $ps->expiration_date->format('M d, Y') }}</span>
                                </div>
                                @if($ps->extension_count > 0)
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">{{ __('student_dashboard.ext_times_extended') }}:</span>
                                    <span>{{ $ps->extension_count }}</span>
                                </div>
                                @endif
                            </div>

                            <span class="badge bg-danger w-100 py-2 mb-3">
                                <i class="bi bi-x-circle me-1"></i>{{ __('student_dashboard.ext_access_expired') }}
                            </span>

                            @if($ps->product->renewal_price && $ps->product->validity_months)
                                <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $ps->product->id]) }}"
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    {{ __('student_dashboard.ext_renew_access') }} - @price($ps->product->renewal_price)
                                </a>
                                <small class="text-muted d-block text-center mt-1">
                                    {{ __('student_dashboard.ext_for_months', ['months' => $ps->product->validity_months]) }}
                                </small>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    {{ __('student_dashboard.ext_not_available') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Expiring Soon Section --}}
    @if($expiringCourses->count() > 0)
    <div class="mb-4">
        <h5 class="text-warning mb-3">
            <i class="bi bi-clock me-2"></i>{{ __('student_dashboard.ext_expiring_soon') }}
        </h5>
        <div class="row">
            @foreach($expiringCourses as $ps)
                @if($ps->product)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                    <div class="card border-warning h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <img src="{{ $ps->product->image ? config('app.file_url') . 'uploads/products/images/' . $ps->product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     class="rounded me-3" width="60" height="60" style="object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $ps->product->titre }}</h6>
                                    @if($ps->product->teacher)
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ps->product->teacher->nom ?? '' }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">{{ __('student_dashboard.ext_expires_on') }}:</span>
                                    <span class="text-warning fw-bold">{{ $ps->expiration_date->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">{{ __('student_dashboard.ext_days_remaining') }}:</span>
                                    <span class="fw-bold">{{ $ps->daysRemaining() }} {{ __('student_dashboard.ext_days') }}</span>
                                </div>
                                @if($ps->extension_count > 0)
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">{{ __('student_dashboard.ext_times_extended') }}:</span>
                                    <span>{{ $ps->extension_count }}</span>
                                </div>
                                @endif
                            </div>

                            <span class="badge bg-warning text-dark w-100 py-2 mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ __('student_dashboard.ext_expiring_in_days', ['days' => $ps->daysRemaining()]) }}
                            </span>

                            @if($ps->product->renewal_price && $ps->product->validity_months)
                                <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $ps->product->id]) }}"
                                   class="btn btn-warning w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    {{ __('student_dashboard.ext_extend_access') }} - @price($ps->product->renewal_price)
                                </a>
                                <small class="text-muted d-block text-center mt-1">
                                    {{ __('student_dashboard.ext_for_months', ['months' => $ps->product->validity_months]) }}
                                </small>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    {{ __('student_dashboard.ext_not_available') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Active Courses Section --}}
    @if($activeCourses->count() > 0)
    <div class="mb-4">
        <h5 class="text-success mb-3">
            <i class="bi bi-check-circle me-2"></i>{{ __('student_dashboard.ext_active_courses') }}
        </h5>
        <div class="row">
            @foreach($activeCourses as $ps)
                @if($ps->product)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                    <div class="card border-success h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <img src="{{ $ps->product->image ? config('app.file_url') . 'uploads/products/images/' . $ps->product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     class="rounded me-3" width="60" height="60" style="object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $ps->product->titre }}</h6>
                                    @if($ps->product->teacher)
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ps->product->teacher->nom ?? '' }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">{{ __('student_dashboard.ext_expires_on') }}:</span>
                                    <span class="text-success fw-bold">{{ $ps->expiration_date->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">{{ __('student_dashboard.ext_days_remaining') }}:</span>
                                    <span class="fw-bold">{{ $ps->daysRemaining() }} {{ __('student_dashboard.ext_days') }}</span>
                                </div>
                            </div>

                            <span class="badge bg-success w-100 py-2 mb-3">
                                <i class="bi bi-check-circle me-1"></i>{{ __('student_dashboard.ext_access_active') }}
                            </span>

                            @if($ps->product->renewal_price && $ps->product->validity_months)
                                <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $ps->product->id]) }}"
                                   class="btn btn-outline-success w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    {{ __('student_dashboard.ext_extend_early') }} - @price($ps->product->renewal_price)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- No courses with expiration --}}
    @if($expiredCourses->count() === 0 && $expiringCourses->count() === 0 && $activeCourses->count() === 0)
    <div class="text-center py-5">
        <i class="bi bi-calendar-check display-1 text-muted"></i>
        <h4 class="mt-3">{{ __('student_dashboard.ext_no_courses') }}</h4>
        <p class="text-muted">{{ __('student_dashboard.ext_no_courses_desc') }}</p>
        <a href="{{ route('student-dashboard.courses.index') }}" class="btn btn-primary mt-2">
            <i class="bi bi-book me-2"></i>{{ __('student_dashboard.my_courses') }}
        </a>
    </div>
    @endif

    {{-- Extension Order History --}}
    @if($extensionOrders->count() > 0)
    <div class="mt-4">
        <h5 class="mb-3">
            <i class="bi bi-clock-history me-2"></i>{{ __('student_dashboard.ext_order_history') }}
        </h5>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('student_dashboard.ext_course') }}</th>
                            <th>{{ __('student_dashboard.ext_amount') }}</th>
                            <th>{{ __('student_dashboard.ext_method') }}</th>
                            <th>{{ __('student_dashboard.ext_extension_period') }}</th>
                            <th>{{ __('student_dashboard.ext_status') }}</th>
                            <th>{{ __('student_dashboard.ext_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($extensionOrders as $order)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $order->product->titre ?? 'N/A' }}</div>
                            </td>
                            <td>@price($order->price)</td>
                            <td>
                                @if($order->payment_method === 'credit_card')
                                    <span class="badge bg-info"><i class="bi bi-credit-card me-1"></i>{{ __('student_dashboard.ext_credit_card') }}</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-bank me-1"></i>{{ __('student_dashboard.ext_bank_transfer') }}</span>
                                @endif
                            </td>
                            <td>{{ $order->extension_months }} {{ __('student_dashboard.ext_months') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status_badge }}">
                                    @if($order->payment_status === 'approved')
                                        <i class="bi bi-check-circle me-1"></i>{{ __('student_dashboard.ext_approved') }}
                                    @elseif($order->payment_status === 'pending')
                                        <i class="bi bi-clock me-1"></i>{{ __('student_dashboard.ext_pending') }}
                                    @elseif($order->payment_status === 'rejected')
                                        <i class="bi bi-x-circle me-1"></i>{{ __('student_dashboard.ext_rejected') }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
