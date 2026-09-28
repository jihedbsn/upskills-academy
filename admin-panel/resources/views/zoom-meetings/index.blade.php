@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="video" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Zoom Meetings</h4>
                                <p class="text-white-50 mb-0">Manage scheduled Zoom meetings for courses</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('zoom-meetings.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Schedule New Meeting
                            </a>
                            <a href="{{ route('zoom-meetings.add-recording') }}" class="btn btn-primary">
                                <i data-feather="video" class="me-2"></i>
                                Add Recording
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meetings Table -->
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($meetings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Topic</th>
                                        <th>Course</th>
                                        <th>Date & Time</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($meetings as $meeting)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $meeting->topic }}</div>
                                                <small class="text-muted">ID: {{ $meeting->zoom_meeting_id }}</small>
                                            </td>
                                            <td>
                                                @if($meeting->product)
                                                    <span class="badge bg-info">{{ $meeting->product->titre }}</span>
                                                @else
                                                    <span class="text-muted">Course deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $meeting->formatted_date }}</div>
                                                <small class="text-muted">{{ $meeting->formatted_time }} ({{ $meeting->timezone }})</small>
                                            </td>
                                            <td>{{ $meeting->duration }} min</td>
                                            <td>
                                                <span class="badge {{ $meeting->status_badge_class }}">
                                                    {{ $meeting->status_label }}
                                                </span>
                                                @if($meeting->recording_url)
                                                <span class="badge bg-primary ms-1" title="Recording available">
                                                    <i data-feather="video" style="width: 12px; height: 12px;"></i> Recording
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($meeting->creator)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i data-feather="user" class="text-muted" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <span>{{ $meeting->creator->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('zoom-meetings.show', $meeting) }}" class="btn btn-sm btn-outline-info" title="View details">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('zoom-meetings.edit', $meeting) }}" class="btn btn-sm btn-outline-warning" title="Edit{{ $meeting->isPast() && !$meeting->recording_url ? ' / Add Recording' : '' }}">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    @if(!$meeting->isPast())
                                                    <a href="{{ $meeting->start_url }}" target="_blank" class="btn btn-sm btn-outline-success" title="Start Meeting">
                                                        <i data-feather="play" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    @endif
                                                    @if($meeting->isPast() && $meeting->recording_url)
                                                    <a href="{{ $meeting->recording_url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Watch Recording">
                                                        <i data-feather="video" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    @endif
                                                    <form action="{{ route('zoom-meetings.destroy', $meeting) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this meeting? This will also remove it from Zoom.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $meetings->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="video" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No scheduled meetings</h5>
                            <p class="text-muted">
                                Schedule a Zoom meeting for your students to get started.
                                <br>
                                <a href="{{ route('zoom-meetings.create') }}" class="btn btn-primary mt-2">
                                    <i data-feather="plus" class="me-1"></i> Schedule First Meeting
                                </a>
                            </p>
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
