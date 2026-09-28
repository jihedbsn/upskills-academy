@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Meeting Details Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Meeting Details</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Topic</label>
                        <p class="fs-5">{{ $zoomMeeting->topic }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Course</label>
                        <p>
                            @if($zoomMeeting->product)
                                <a href="{{ route('products.show', $zoomMeeting->product) }}" class="text-primary">
                                    {{ $zoomMeeting->product->titre }}
                                </a>
                            @else
                                <span class="text-muted">Course deleted</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Date & Time</label>
                        <p>{{ $zoomMeeting->formatted_date }}<br>{{ $zoomMeeting->formatted_time }} ({{ $zoomMeeting->timezone }})</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Duration</label>
                        <p>{{ $zoomMeeting->duration }} minutes</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Status</label>
                        <div>
                            <span class="badge {{ $zoomMeeting->status_badge_class }}">
                                {{ $zoomMeeting->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Moderator</label>
                        <p><a href="mailto:{{ $zoomMeeting->moderator_email }}">{{ $zoomMeeting->moderator_email }}</a></p>
                    </div>

                    @if($zoomMeeting->agenda)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Agenda</label>
                        <p class="text-muted">{{ $zoomMeeting->agenda }}</p>
                    </div>
                    @endif

                    <hr>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Meeting ID</label>
                        <p class="font-monospace">{{ $zoomMeeting->zoom_meeting_id }}</p>
                    </div>

                    <div class="alert alert-info mb-3">
                        <small><i class="bi bi-info-circle"></i> Meeting password is embedded in the join URL</small>
                    </div>

                    @if($zoomMeeting->recording_url)
                    <div class="alert alert-success mb-3">
                        <label class="fw-bold mb-2"><i data-feather="video" class="me-1"></i> Recording Available</label>
                        <a href="{{ $zoomMeeting->recording_url }}" target="_blank" class="btn btn-sm btn-success w-100">
                            <i data-feather="play-circle" class="me-1"></i> Watch Recording
                        </a>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ $zoomMeeting->start_url }}" target="_blank" class="btn btn-success">
                            <i data-feather="play" class="me-1"></i> Start Meeting
                        </a>
                        <a href="{{ $zoomMeeting->join_url }}" target="_blank" class="btn btn-outline-primary">
                            <i data-feather="external-link" class="me-1"></i> Join as Participant
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('zoom-meetings.edit', $zoomMeeting) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-1"></i> Edit Meeting
                        </a>
                        <form action="{{ route('zoom-meetings.destroy', $zoomMeeting) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this meeting? This will also remove it from Zoom.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i data-feather="trash-2" class="me-1"></i> Delete Meeting
                            </button>
                        </form>
                        <a href="{{ route('zoom-meetings.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Enrolled Students Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Notified Students</h4>
                    <span class="badge bg-primary">{{ $enrolledStudents->count() }} Students</span>
                </div>
                <div class="card-body">
                    @if($enrolledStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledStudents as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="fw-bold text-primary">{{ substr($student->name, 0, 1) }}</span>
                                                    </div>
                                                    {{ $student->name }}
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                <span class="badge bg-success">Enrolled</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i data-feather="users" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                            <p class="text-muted">No active students enrolled in this course.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
@endpush
