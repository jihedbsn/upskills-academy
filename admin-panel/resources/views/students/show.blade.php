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

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small">ACCOUNT STATUS</label>
                                    <div class="d-flex align-items-center">
                                        @if($student->is_blocked)
                                            <span class="badge bg-danger">
                                                <i data-feather="lock" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Blocked
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i data-feather="check-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($student->is_blocked)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">BLOCK REASON</label>
                                        <div class="alert alert-danger mb-0">
                                            <small>{{ $student->block_reason }}</small>
                                        </div>
                                        <small class="text-muted">Blocked on {{ $student->blocked_at->format('M d, Y H:i') }}</small>
                                    </div>
                                @endif
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
                                <a href="{{ route('emails.index', ['email' => $student->email]) }}" class="btn btn-outline-primary">
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

                                @if($student->is_blocked)
                                    <form action="{{ route('students.unblock', $student) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success w-100" onclick="return confirm('Are you sure you want to unblock this student? They will be able to log in again.')">
                                            <i data-feather="unlock" class="me-2"></i>
                                            Unblock Account
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#blockStudentModal">
                                        <i data-feather="lock" class="me-2"></i>
                                        Block Account
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="col-lg-8">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

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

                    <!-- Customer Points Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">
                                    <i data-feather="star" class="me-2"></i>
                                    Customer Points
                                </h5>
                                <span class="badge bg-white text-dark">
                                    <i data-feather="dollar-sign" class="me-1" style="width: 12px; height: 12px;"></i>
                                    ${{ number_format($pointsBalance['available_discount'], 2) }} Available Discount
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Points Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h3 class="text-primary mb-1">{{ number_format($pointsBalance['available_points']) }}</h3>
                                        <small class="text-muted">Available Points</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h3 class="text-success mb-1">{{ number_format($pointsBalance['total_points']) }}</h3>
                                        <small class="text-muted">Total Earned</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h3 class="text-info mb-1">{{ number_format($pointsBalance['used_points']) }}</h3>
                                        <small class="text-muted">Points Used</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Adjust Points Form -->
                            <form action="{{ route('students.adjust-points', $student) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Points Adjustment</label>
                                        <input type="number" name="points" class="form-control" placeholder="e.g., 50 or -20" required>
                                        <small class="text-muted">Positive to add, negative to deduct</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Reason</label>
                                        <input type="text" name="reason" class="form-control" placeholder="Reason for adjustment" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i data-feather="edit" class="me-2" style="width: 14px; height: 14px;"></i>
                                            Adjust Points
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Points History -->
                            @if($pointsHistory->count() > 0)
                                <h6 class="text-muted mb-3">Recent Points Activity</h6>
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Points</th>
                                                <th>Description</th>
                                                <th>Balance After</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pointsHistory as $transaction)
                                                <tr>
                                                    <td class="small">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        @php
                                                            $badgeClass = match($transaction->type) {
                                                                'earn' => 'bg-success',
                                                                'redeem' => 'bg-danger',
                                                                'adjust' => 'bg-warning',
                                                                'expire' => 'bg-secondary',
                                                                default => 'bg-info'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($transaction->type) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($transaction->points > 0)
                                                            <span class="text-success fw-bold">+{{ $transaction->points }}</span>
                                                        @else
                                                            <span class="text-danger fw-bold">{{ $transaction->points }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="small">{{ Str::limit($transaction->description, 40) }}</td>
                                                    <td><span class="badge bg-light text-dark">{{ number_format($transaction->balance_after) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i data-feather="inbox" class="mb-2" style="width: 32px; height: 32px;"></i>
                                    <p class="mb-0">No points activity yet</p>
                                </div>
                            @endif

                            <hr class="my-4">
                            <h6 class="text-muted mb-3">Auto Deduction Usage (Recent Orders)</h6>
                            @if(isset($pointsUsageOrders) && $pointsUsageOrders->count() > 0)
                                <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Order</th>
                                                <th>Points Used</th>
                                                <th>Discount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pointsUsageOrders as $usageOrder)
                                                <tr>
                                                    <td>#{{ $usageOrder->id }}</td>
                                                    <td><span class="badge bg-warning text-dark">{{ number_format($usageOrder->points_used ?? 0) }}</span></td>
                                                    <td><span class="text-success">-${{ number_format((float) ($usageOrder->points_discount ?? 0), 2) }}</span></td>
                                                    <td class="small">{{ optional($usageOrder->created_at)->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-muted small">No orders used points yet.</div>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <small class="text-muted">
                                <i data-feather="info" class="me-1" style="width: 12px; height: 12px;"></i>
                                Points earned: 1 point per $1 spent | Points value: 20 points = $1 discount
                            </small>
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
                                                @php $courseIsBlocked = isset($blockedCourseIds[$order->product->id]); @endphp
                                                <tr class="{{ $courseIsBlocked ? 'course-row-blocked' : '' }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm {{ $courseIsBlocked ? 'bg-danger' : 'bg-primary' }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                                                <i data-feather="{{ $courseIsBlocked ? 'slash' : 'book' }}" class="text-white" style="width: 16px; height: 16px;"></i>
                                                            </div>
                                                            <div>
                                                                <strong class="{{ $courseIsBlocked ? 'text-danger' : '' }}">
                                                                    {{ $order->product->name_en ?? $order->product->name_ar ?? $order->product->titre ?? 'Course #' . $order->product->id }}
                                                                </strong>
                                                                @if($courseIsBlocked)
                                                                    <small class="d-block text-danger fw-semibold">
                                                                        <i data-feather="lock" style="width: 12px; height: 12px;"></i>
                                                                        Blocked from this course
                                                                    </small>
                                                                @endif
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

                    <!-- Course Block Management -->
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger bg-opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-danger">
                                    <i data-feather="shield-off" class="me-2"></i>
                                    Course Access Blocks
                                </h5>
                                <span class="badge bg-danger">{{ $blockedCourses->count() }} blocked</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">
                                Block this student from specific enrolled courses. Blocked courses remain in their enrollment list but access is denied.
                            </p>

                            <!-- Search & block -->
                            <div class="mb-4">
                                <label for="courseBlockSearch" class="form-label fw-semibold">
                                    <i data-feather="search" class="me-1" style="width: 14px; height: 14px;"></i>
                                    Search enrolled course to block
                                </label>
                                <div class="position-relative">
                                    <input type="text"
                                           id="courseBlockSearch"
                                           class="form-control form-control-lg"
                                           placeholder="Search by course name or ID..."
                                           autocomplete="off">
                                    <div id="courseBlockSearchResults" class="course-block-search-results d-none"></div>
                                </div>
                                <small class="text-muted">
                                    @if($blockableCourses->count() > 0)
                                        {{ $blockableCourses->count() }} course(s) available to block. Click the field to browse or type to filter.
                                    @else
                                        No blockable courses — either none enrolled or all are already blocked.
                                    @endif
                                </small>
                            </div>

                            <!-- Blocked courses list -->
                            <h6 class="fw-semibold mb-3">
                                <i data-feather="list" class="me-1" style="width: 14px; height: 14px;"></i>
                                Blocked Courses ({{ $blockedCourses->count() }})
                            </h6>

                            @if($blockedCourses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Course</th>
                                                <th>Blocked at</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($blockedCourses as $block)
                                                <tr class="course-row-blocked">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center me-3">
                                                                <i data-feather="lock" class="text-white" style="width: 14px; height: 14px;"></i>
                                                            </div>
                                                            <div>
                                                                <strong class="text-danger">
                                                                    {{ $block->course->titre ?? 'Course #' . $block->course_id }}
                                                                </strong>
                                                                <br>
                                                                <small class="text-muted">ID: {{ $block->course_id }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $block->created_at->format('M d, Y H:i') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <form method="POST"
                                                              action="{{ route('students.toggle-block-course', [$student, $block->course_id]) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-success"
                                                                    onclick="return confirm('Unblock this student from this course?')">
                                                                <i data-feather="unlock" class="me-1" style="width: 14px; height: 14px;"></i>
                                                                Unblock
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('course-students.show', $block->course_id) }}"
                                                           class="btn btn-sm btn-outline-primary ms-1"
                                                           title="View course students">
                                                            <i data-feather="external-link" style="width: 14px; height: 14px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded">
                                    <i data-feather="check-circle" class="text-success mb-2" style="width: 40px; height: 40px;"></i>
                                    <p class="text-muted mb-0">No courses are blocked for this student.</p>
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

                    @include('students.partials.email-history')
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

.course-row-blocked {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.03) 100%) !important;
    border-left: 3px solid #dc3545;
}

.course-block-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    max-height: 320px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    margin-top: 4px;
}

.course-block-search-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f3f5;
    gap: 1rem;
}

.course-block-search-item:last-child {
    border-bottom: none;
}

.course-block-search-item:hover {
    background-color: #f8f9fa;
}

.course-block-search-empty {
    padding: 1rem;
    text-align: center;
    color: #6c757d;
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

<!-- Block Student Modal -->
<div class="modal fade" id="blockStudentModal" tabindex="-1" aria-labelledby="blockStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('students.block', $student) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="blockStudentModalLabel">
                        <i data-feather="lock" class="me-2"></i>
                        Block Student Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i data-feather="alert-triangle" class="me-2"></i>
                        <strong>Warning:</strong> This will immediately log out the student and prevent them from logging in until unblocked.
                    </div>

                    <div class="mb-3">
                        <label for="block_reason" class="form-label">Reason for Blocking <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="block_reason" name="block_reason" rows="4" required placeholder="Enter the reason for blocking this student account..."></textarea>
                        <small class="text-muted">This reason will be stored for reference.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i data-feather="lock" class="me-2"></i>
                        Block Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();

    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        const searchInput = document.getElementById('courseBlockSearch');
        const resultsBox = document.getElementById('courseBlockSearchResults');
        const allBlockableCourses = @json($blockableCourses);
        const blockUrlTemplate = @json(route('students.toggle-block-course', [$student, '__COURSE_ID__']));
        const csrfToken = @json(csrf_token());
        let searchTimeout = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterCourses(query) {
            const q = query.trim().toLowerCase();

            if (!q) {
                return allBlockableCourses;
            }

            return allBlockableCourses.filter(function(course) {
                return String(course.id).includes(q)
                    || course.title.toLowerCase().includes(q);
            });
        }

        function renderResults(courses, emptyMessage) {
            if (!courses.length) {
                resultsBox.innerHTML = '<div class="course-block-search-empty">' + escapeHtml(emptyMessage || 'No enrolled course matches your search.') + '</div>';
                resultsBox.classList.remove('d-none');
                return;
            }

            resultsBox.innerHTML = courses.map(function(course) {
                const priceLabel = course.price > 0
                    ? '$' + Number(course.price).toFixed(2)
                    : 'Free';
                const blockUrl = blockUrlTemplate.replace('__COURSE_ID__', course.id);

                return `
                    <div class="course-block-search-item">
                        <div>
                            <strong>${escapeHtml(course.title)}</strong>
                            <br>
                            <small class="text-muted">ID: ${course.id} · ${priceLabel}</small>
                        </div>
                        <form method="POST" action="${blockUrl}" class="mb-0">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Block this student from this course?')">
                                <i data-feather="lock" style="width: 14px; height: 14px;"></i>
                                Block
                            </button>
                        </form>
                    </div>
                `;
            }).join('');

            resultsBox.classList.remove('d-none');
            feather.replace();
        }

        function searchCourses(query) {
            const courses = filterCourses(query);
            let emptyMessage = 'No enrolled course matches your search.';

            if (!allBlockableCourses.length) {
                emptyMessage = 'This student has no enrolled courses available to block.';
            } else if (!courses.length && !query.trim()) {
                emptyMessage = 'All enrolled courses are already blocked.';
            }

            renderResults(courses, emptyMessage);
        }

        if (searchInput && resultsBox) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;

                searchTimeout = setTimeout(function() {
                    searchCourses(query);
                }, 200);
            });

            searchInput.addEventListener('focus', function() {
                searchCourses(this.value);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush
