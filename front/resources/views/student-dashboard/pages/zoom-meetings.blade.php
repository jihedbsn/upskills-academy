@extends('student-dashboard.layouts.app')

@section('title', __('messages.zoom_meetings'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-camera-video" style="color: #0071ae;"></i> {{ __('messages.zoom_meetings') }}
                    </h2>
                    <p class="text-muted mb-0">{{ __('messages.manage_zoom_meetings') }}</p>
                </div>
                <div>
                    <span class="badge fs-6" style="background-color: #0071ae;">{{ $upcomingMeetings->count() }} {{ __('messages.upcoming') }}</span>
                    <span class="badge bg-secondary fs-6">{{ $pastMeetings->count() }} {{ __('messages.past') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Meetings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #0071ae 0%, #005a8c 100%);">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check"></i> {{ __('messages.upcoming_zoom_sessions') }}
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($upcomingMeetings as $meeting)
                    <div class="zoom-meeting-card border rounded p-4 mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="meeting-icon rounded-circle p-3" style="background-color: rgba(0, 113, 174, 0.1); color: #0071ae;">
                                            <i class="bi bi-camera-video fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="mb-2">{{ $meeting->topic }}</h4>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-book" style="color: #0071ae;"></i>
                                            <strong>{{ __('messages.course') }}:</strong> {{ $meeting->product->titre ?? 'N/A' }}
                                        </p>

                                        <div class="meeting-details">
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-4">
                                                    <div class="detail-item">
                                                        <i class="bi bi-calendar3" style="color: #0071ae;"></i>
                                                        <span class="text-muted small">{{ __('messages.date') }}</span>
                                                        <strong class="d-block">{{ $meeting->formatted_date }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="detail-item">
                                                        <i class="bi bi-clock" style="color: #0071ae;"></i>
                                                        <span class="text-muted small">{{ __('messages.time') }}</span>
                                                        <strong class="d-block">{{ $meeting->formatted_time }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="detail-item">
                                                        <i class="bi bi-hourglass-split" style="color: #0071ae;"></i>
                                                        <span class="text-muted small">{{ __('messages.duration') }}</span>
                                                        <strong class="d-block">{{ $meeting->duration }} {{ __('messages.minutes') }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($meeting->agenda)
                                        <div class="alert alert-light mb-3">
                                            <strong><i class="bi bi-list-task"></i> {{ __('messages.agenda') }}:</strong>
                                            <p class="mb-0 mt-2">{{ $meeting->agenda }}</p>
                                        </div>
                                        @endif

                                        @if($meeting->moderator_email)
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-person-badge"></i>
                                            <strong>{{ __('messages.moderator') }}:</strong>
                                            <a href="mailto:{{ $meeting->moderator_email }}">{{ $meeting->moderator_email }}</a>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="meeting-actions">
                                    <div class="d-grid gap-2">
                                        <a href="{{ $meeting->join_url }}" target="_blank" class="btn btn-lg" style="background-color: #0071ae; border-color: #0071ae; color: white;">
                                            <i class="bi bi-box-arrow-up-right"></i> {{ __('messages.join_meeting') }}
                                        </a>

                                        <div class="countdown-timer rounded p-3 text-center" style="background-color: rgba(252, 209, 22, 0.15); border: 2px dashed #FCD116;">
                                            <small class="text-muted d-block mb-1">{{ __('messages.time_until_meeting') }}</small>
                                            <strong class="countdown" data-time="{{ $meeting->start_time->toIso8601String() }}" style="color: #0071ae;">
                                                Calculating...
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">{{ __('messages.no_upcoming_meetings') }}</h5>
                        <p class="text-muted">{{ __('messages.no_scheduled_zoom') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Past Meetings -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> {{ __('messages.past_zoom_sessions') }}
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($pastMeetings as $meeting)
                    <div class="past-meeting-item border-bottom pb-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="meeting-icon-small bg-secondary bg-opacity-10 text-secondary rounded p-2">
                                            <i class="bi bi-camera-video fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $meeting->topic }}</h6>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-book"></i> {{ $meeting->product->titre ?? 'N/A' }}
                                        </p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-calendar"></i> {{ $meeting->formatted_date }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-clock"></i> {{ $meeting->formatted_time }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-hourglass"></i> {{ $meeting->duration }} min
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Completed
                                            </span>
                                        </div>
                                        @if($meeting->agenda)
                                        <p class="text-muted small mt-2 mb-0">
                                            <strong>Agenda:</strong> {{ Str::limit($meeting->agenda, 150) }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                @if($meeting->recording_url)
                                <a href="{{ $meeting->recording_url }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                                    <i class="bi bi-play-circle"></i> {{ __('messages.watch_recording') }}
                                </a>
                                <br>
                                @endif
                                <span class="text-muted small">
                                    {{ $meeting->start_time->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">{{ __('messages.no_past_meetings') }}</h5>
                        <p class="text-muted">{{ __('messages.completed_zoom_appear') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.zoom-meeting-card {
    transition: all 0.3s ease;
    background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
}

.zoom-meeting-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 113, 174, 0.15) !important;
    transform: translateY(-2px);
    border-color: #0071ae !important;
}

.meeting-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.meeting-icon-small {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.detail-item {
    padding: 0.75rem;
    background: rgba(0, 113, 174, 0.03);
    border-radius: 0.5rem;
    border: 1px solid rgba(0, 113, 174, 0.1);
}

.detail-item i {
    display: block;
    margin-bottom: 0.25rem;
    font-size: 1.25rem;
}

.btn-outline-secondary:hover {
    background-color: #0071ae !important;
    border-color: #0071ae !important;
    color: white !important;
}

.past-meeting-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}

.past-meeting-item:hover {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem !important;
    margin: 0 -1rem 1rem -1rem !important;
}
</style>
@endpush

@push('scripts')
<script>
// Countdown timer
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(function(element) {
        const targetTime = new Date(element.dataset.time);
        const now = new Date();
        const diff = targetTime - now;

        if (diff > 0) {
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            let countdown = '';
            if (days > 0) {
                countdown = `${days}d ${hours}h ${minutes}m`;
            } else if (hours > 0) {
                countdown = `${hours}h ${minutes}m ${seconds}s`;
            } else {
                countdown = `${minutes}m ${seconds}s`;
            }

            element.textContent = countdown;
        } else {
            element.textContent = 'Meeting time!';
            element.parentElement.classList.remove('bg-warning');
            element.parentElement.classList.add('bg-success', 'text-white');
        }
    });
}

// Update countdowns every second
setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>
@endpush
