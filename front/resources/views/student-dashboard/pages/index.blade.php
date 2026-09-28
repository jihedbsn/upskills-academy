@extends('student-dashboard.layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">{{ __('messages.welcome_back') }}, {{ Auth::user()->full_name ?? 'Student' }}!</h2>
                            <p class="card-text mb-0">{{ __('messages.dashboard_overview') }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="dashboard-date">
                                <h4 class="mb-1">{{ now()->format('d') }}</h4>
                                <p class="mb-0">{{ now()->format('M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($unfinishedAttempts) && $unfinishedAttempts->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center justify-content-between mb-0">
                    <div>
                        <i class="bi bi-hourglass-split me-2"></i>
                        <strong>{{ __('messages.unfinished_quiz_attempts') }}:</strong>
                        {{ $unfinishedAttempts->count() }}
                    </div>
                    <a href="{{ route('student-dashboard.history.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('messages.history') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $stats['enrolled_courses'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.enrolled_courses') }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $stats['completed_courses'] }} {{ __('messages.completed') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $stats['completed_courses'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.certificates_earned') }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-warning">
                                <i class="bi bi-clock"></i> {{ $stats['pending_certificates'] }} {{ __('messages.pending') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ number_format($stats['average_quiz_score'], 1) }}%</h3>
                            <p class="text-muted mb-0">{{ __('messages.average_score') }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-info">
                                <i class="bi bi-check-circle"></i> {{ $stats['successful_quizzes'] }}/{{ $stats['total_quiz_attempts'] }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">@price($stats['total_spent'])</h3>
                            <p class="text-muted mb-0">{{ __('messages.total_invested') }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i> {{ $stats['successful_orders'] }}/{{ $stats['total_orders'] }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-clock-history text-primary fs-1 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($stats['study_hours'], 1) }}h</h5>
                    <p class="text-muted mb-0">{{ __('messages.study_hours') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-ticket text-warning fs-1 mb-2"></i>
                    <h5 class="mb-1">{{ $stats['open_tickets'] }}</h5>
                    <p class="text-muted mb-0">{{ __('messages.open_tickets') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                    <h5 class="mb-1">{{ $stats['resolved_tickets'] }}</h5>
                    <p class="text-muted mb-0">{{ __('messages.resolved_tickets') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-event text-info fs-1 mb-2"></i>
                    <h5 class="mb-1">{{ $upcomingDeadlines->count() }}</h5>
                    <p class="text-muted mb-0">{{ __('messages.upcoming_deadlines') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Points Section -->
    @if(isset($pointsBalance))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #297a7b 0%, #3cb4b0 100%);">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end border-white border-opacity-25">
                            <i class="bi bi-star-fill fs-1 mb-2"></i>
                            <h2 class="mb-0 fw-bold">{{ number_format($pointsBalance['available_points']) }}</h2>
                            <p class="mb-0 small">{{ __('messages.available_points') ?? 'Available Points' }}</p>
                        </div>
                        <div class="col-md-3 text-center border-end border-white border-opacity-25">
                            <h4 class="mb-0">@price($pointsBalance['available_discount'])</h4>
                            <p class="mb-0 small">{{ __('messages.available_discount') ?? 'Available Discount' }}</p>
                        </div>
                        <div class="col-md-3 text-center border-end border-white border-opacity-25">
                            <h4 class="mb-0">{{ number_format($pointsBalance['total_points']) }}</h4>
                            <p class="mb-0 small">{{ __('messages.total_earned') ?? 'Total Earned' }}</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <p class="mb-1 small"><i class="bi bi-info-circle me-1"></i>{{ __('messages.points_info') ?? 'Earn 1 point per $1 spent' }}</p>
                            <p class="mb-0 small">{{ __('messages.points_value') ?? '20 points = $1 discount' }}</p>
                            <a href="{{ route('checkout.index', ['locale' => app()->getLocale()]) }}" class="btn btn-light btn-sm mt-2">
                                <i class="bi bi-cart me-1"></i>{{ __('messages.use_points') ?? 'Use Points at Checkout' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Points History (if any) -->
    @if(isset($pointsHistory) && count($pointsHistory) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>{{ __('messages.recent_points_activity') ?? 'Recent Points Activity' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.date') ?? 'Date' }}</th>
                                    <th>{{ __('messages.type') ?? 'Type' }}</th>
                                    <th>{{ __('messages.points') ?? 'Points' }}</th>
                                    <th>{{ __('messages.description') ?? 'Description' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pointsHistory as $transaction)
                                    <tr>
                                        <td class="small">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</td>
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
                                        <td class="small">{{ Str::limit($transaction->description, 50) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- Zoom Meetings Section -->
    @if(isset($upcomingZoomMeetings) && $upcomingZoomMeetings->count() > 0 || isset($pastZoomMeetings) && $pastZoomMeetings->count() > 0)
    <div class="row mb-4">
        <!-- Upcoming Zoom Meetings -->
        @if(isset($upcomingZoomMeetings) && $upcomingZoomMeetings->count() > 0)
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="bi bi-camera-video text-primary"></i> {{ __('messages.upcoming_zoom_sessions') }}
                        </h5>
                        <span class="badge bg-primary">{{ $upcomingZoomMeetings->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($upcomingZoomMeetings as $meeting)
                    <div class="zoom-meeting-item border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="zoom-icon bg-primary bg-opacity-10 text-primary rounded p-3">
                                    <i class="bi bi-camera-video fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $meeting->topic }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-book"></i> {{ $meeting->product->titre ?? 'N/A' }}
                                </p>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-calendar"></i> {{ $meeting->formatted_date }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-clock"></i> {{ $meeting->formatted_time }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-hourglass"></i> {{ $meeting->duration }} min
                                    </span>
                                </div>
                                @if($meeting->agenda)
                                <p class="text-muted small mb-2">{{ Str::limit($meeting->agenda, 100) }}</p>
                                @endif
                                <div class="d-flex gap-2">
                                    <a href="{{ $meeting->join_url }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right"></i> {{ __('messages.join_meeting') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Past Zoom Meetings -->
        @if(isset($pastZoomMeetings) && $pastZoomMeetings->count() > 0)
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history text-secondary"></i> {{ __('messages.past_zoom_sessions') }}
                        </h5>
                        <span class="badge bg-secondary">{{ $pastZoomMeetings->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($pastZoomMeetings as $meeting)
                    <div class="zoom-meeting-item border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="zoom-icon bg-secondary bg-opacity-10 text-secondary rounded p-3">
                                    <i class="bi bi-camera-video fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $meeting->topic }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-book"></i> {{ $meeting->product->titre ?? 'N/A' }}
                                </p>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-calendar"></i> {{ $meeting->formatted_date }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-clock"></i> {{ $meeting->formatted_time }}
                                    </span>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-check-circle"></i> Completed
                                    </span>
                                </div>
                                @if($meeting->agenda)
                                <p class="text-muted small mb-0">{{ Str::limit($meeting->agenda, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="row">
        <!-- Progress Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('messages.monthly_progress') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="progressChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Courses -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('messages.recent_courses') }}</h5>
                    <a href="{{ route('student-dashboard.courses.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentCourses as $courseData)
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $courseData['product']->titre }}</h6>
                                    <small class="text-muted">{{ $courseData['product']->category->name_en ?? 'No Category' }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-{{ $courseData['status'] == 'completed' ? 'success' : ($courseData['status'] == 'pending' ? 'warning' : 'primary') }}"
                                             style="width: {{ $courseData['progress'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $courseData['progress'] }}%</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-{{ $courseData['status'] == 'completed' ? 'success' : ($courseData['status'] == 'pending' ? 'warning' : 'primary') }}">
                                    @if($courseData['status'] == 'completed')
                                        {{ __('messages.completed') }}
                                    @elseif($courseData['status'] == 'pending')
                                        {{ __('messages.pending') }}
                                    @else
                                        {{ __('messages.in_progress') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-book fs-1 mb-2"></i>
                            <p>{{ __('messages.no_courses_enrolled') }}</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm">{{ __('messages.browse_courses') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('messages.recent_activity') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="d-flex align-items-center mb-3">
                            <div class="activity-icon me-3">
                                <i class="{{ $activity['icon'] }} fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                                @if(isset($activity['score']))
                                    <small class="badge bg-info">{{ $activity['score'] }}%</small>
                                @endif
                                @if(isset($activity['submission_status_label']))
                                    <small class="badge bg-{{ $activity['submission_status_badge'] ?? 'secondary' }} ms-1">
                                        {{ $activity['submission_status_label'] }}
                                    </small>
                                @endif
                                @if(isset($activity['amount']))
                                    <small class="badge bg-{{ $activity['status'] == 'success' ? 'success' : 'danger' }}">@price($activity['amount'])</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-activity fs-1 mb-2"></i>
                            <p>{{ __('messages.no_recent_activity') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines & Recommended Courses -->
        <div class="col-lg-4 mb-4">
            <!-- Upcoming Deadlines -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('messages.upcoming_deadlines') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($upcomingDeadlines as $deadline)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="{{ $deadline['icon'] }} fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $deadline['title'] }}</h6>
                                <small class="text-muted">{{ $deadline['description'] }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $deadline['date']->format('M d') }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-check fs-1 mb-2"></i>
                            <p>{{ __('messages.no_upcoming_deadlines') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recommended Courses -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('messages.recommended_courses') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($recommendedCourses as $course)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-book text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $course->titre }}</h6>
                                <small class="text-muted">{{ $course->category->name_en ?? 'No Category' }}</small>
                            </div>
                            <div>
                                <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $course->id]) }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('messages.view') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-star fs-1 mb-2"></i>
                            <p>{{ __('messages.no_recommendations') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Copy to clipboard function for Zoom password
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Progress Chart
    const ctx = document.getElementById('progressChart').getContext('2d');
    const progressChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! $monthlyProgress->pluck('month')->toJson() !!},
            datasets: [{
                label: '{{ __("messages.quiz_attempts") }}',
                data: {!! $monthlyProgress->pluck('quiz_attempts')->toJson() !!},
                borderColor: 'rgb(0, 113, 174)',
                backgroundColor: 'rgba(0, 113, 174, 0.1)',
                tension: 0.4
            }, {
                label: '{{ __("messages.certificates_earned") }}',
                data: {!! $monthlyProgress->pluck('certificates')->toJson() !!},
                borderColor: 'rgb(252, 209, 22)',
                backgroundColor: 'rgba(252, 209, 22, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.activity-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dashboard-date {
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem;
    border-radius: 0.5rem;
}

.card {
    border-radius: 0.75rem;
}

.progress {
    border-radius: 0.25rem;
}

.badge {
    font-size: 0.75rem;
}

/* Zoom Meeting Styles */
.zoom-meeting-item {
    transition: all 0.2s ease;
}

.zoom-meeting-item:hover {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 0.5rem !important;
    margin: 0 -0.5rem 0.75rem -0.5rem !important;
}

.zoom-icon {
    transition: transform 0.2s ease;
}

.zoom-meeting-item:hover .zoom-icon {
    transform: scale(1.1);
}

.zoom-meeting-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}
</style>
@endpush
