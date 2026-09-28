@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Session Details Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Session Details</h4>
                    <span class="badge bg-{{ $courseSession->getStatusBadgeColor() }}">
                        {{ $courseSession->getStatusLabel() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Title</label>
                        <p class="fs-5">{{ $courseSession->title }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Course</label>
                        <p>
                            @if($courseSession->product)
                                <a href="{{ route('products.show', $courseSession->product) }}" class="text-primary">
                                    {{ $courseSession->product->titre }}
                                </a>
                            @else
                                <span class="text-muted">Course deleted</span>
                            @endif
                        </p>
                    </div>

                    @if($courseSession->description)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Description</label>
                        <p>{{ $courseSession->description }}</p>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Date</label>
                            <p>{{ $courseSession->formatted_date }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Time</label>
                            <p>{{ $courseSession->formatted_time }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Type</label>
                            <p><span class="badge bg-info">{{ $courseSession->getTypeLabel() }}</span></p>
                        </div>
                        @if($courseSession->instructor_name)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Instructor</label>
                            <p>{{ $courseSession->instructor_name }}</p>
                        </div>
                        @endif
                    </div>

                    @if($courseSession->session_type === 'classroom' && $courseSession->location)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Location</label>
                        <p><i data-feather="map-pin" class="me-1"></i> {{ $courseSession->location }}</p>
                    </div>
                    @endif

                    @if($courseSession->session_type === 'online')
                    <hr>
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Online Access</label>
                        @if($courseSession->zoom_meeting_id)
                            <p class="font-monospace">Meeting ID: {{ $courseSession->zoom_meeting_id }}</p>
                        @endif
                        @if($courseSession->zoom_join_url)
                            <a href="{{ $courseSession->zoom_join_url }}" target="_blank" class="btn btn-primary">
                                <i data-feather="video" class="me-1"></i> Join Online Session
                            </a>
                        @endif
                    </div>
                    @endif

                    @if($courseSession->notes)
                    <hr>
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Additional Notes</label>
                        <p class="text-muted">{{ $courseSession->notes }}</p>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <strong>Created:</strong> {{ $courseSession->created_at->format('M d, Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong> {{ $courseSession->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.course-sessions.edit', $courseSession) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-1"></i> Edit Session
                        </a>
                        <form action="{{ route('admin.course-sessions.destroy', $courseSession) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this session? Students will be notified about the cancellation.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i data-feather="trash-2" class="me-1"></i> Delete Session
                            </button>
                        </form>
                        <a href="{{ route('admin.course-sessions.index') }}" class="btn btn-outline-secondary ms-auto">
                            <i data-feather="arrow-left" class="me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Enrolled Students Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enrolled Students ({{ $enrolledStudents->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($enrolledStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($enrolledStudents->take(10) as $student)
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $student->name }}</div>
                                            <small class="text-muted">{{ $student->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($enrolledStudents->count() > 10)
                            <p class="text-muted small mt-2 mb-0">
                                ... and {{ $enrolledStudents->count() - 10 }} more students
                            </p>
                        @endif
                    @else
                        <p class="text-muted">No students enrolled in this course yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
