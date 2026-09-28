@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Course Session</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i data-feather="alert-circle" class="me-1"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.course-sessions.update', $courseSession) }}">
                        @csrf
                        @method('PUT')

                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Select a course...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ (old('product_id', $courseSession->product_id) == $product->id) ? 'selected' : '' }}>
                                        {{ $product->titre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Students will be notified if important details change.</div>
                        </div>

                        <!-- Session Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Session Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $courseSession->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $courseSession->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Session Date -->
                            <div class="col-md-4 mb-3">
                                <label for="session_date" class="form-label">Session Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('session_date') is-invalid @enderror" id="session_date" name="session_date" value="{{ old('session_date', $courseSession->session_date->format('Y-m-d')) }}" required>
                                @error('session_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $courseSession->start_time->format('H:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $courseSession->end_time->format('H:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Session Type -->
                            <div class="col-md-4 mb-3">
                                <label for="session_type" class="form-label">Session Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('session_type') is-invalid @enderror" id="session_type" name="session_type" required>
                                    <option value="">Select type...</option>
                                    <option value="theory" {{ old('session_type', $courseSession->session_type) == 'theory' ? 'selected' : '' }}>Theory</option>
                                    <option value="practical" {{ old('session_type', $courseSession->session_type) == 'practical' ? 'selected' : '' }}>Practical</option>
                                    <option value="online" {{ old('session_type', $courseSession->session_type) == 'online' ? 'selected' : '' }}>Online (Zoom/Virtual)</option>
                                    <option value="classroom" {{ old('session_type', $courseSession->session_type) == 'classroom' ? 'selected' : '' }}>Classroom</option>
                                </select>
                                @error('session_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $courseSession->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="ongoing" {{ old('status', $courseSession->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ old('status', $courseSession->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $courseSession->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Instructor Name -->
                            <div class="col-md-4 mb-3">
                                <label for="instructor_name" class="form-label">Instructor Name</label>
                                <input type="text" class="form-control @error('instructor_name') is-invalid @enderror" id="instructor_name" name="instructor_name" value="{{ old('instructor_name', $courseSession->instructor_name) }}">
                                @error('instructor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location (for classroom) -->
                        <div class="mb-3" id="location_field" style="display: none;">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $courseSession->location) }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Online Session Fields -->
                        <div id="online_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="zoom_meeting_id" class="form-label">Zoom Meeting ID</label>
                                    <input type="text" class="form-control @error('zoom_meeting_id') is-invalid @enderror" id="zoom_meeting_id" name="zoom_meeting_id" value="{{ old('zoom_meeting_id', $courseSession->zoom_meeting_id) }}">
                                    @error('zoom_meeting_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="zoom_join_url" class="form-label">Zoom Join URL</label>
                                    <input type="url" class="form-control @error('zoom_join_url') is-invalid @enderror" id="zoom_join_url" name="zoom_join_url" value="{{ old('zoom_join_url', $courseSession->zoom_join_url) }}">
                                    @error('zoom_join_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $courseSession->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.course-sessions.show', $courseSession) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1"></i> Update Session
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionTypeSelect = document.getElementById('session_type');
    const locationField = document.getElementById('location_field');
    const onlineFields = document.getElementById('online_fields');

    function toggleFields() {
        const selectedType = sessionTypeSelect.value;

        // Show/hide location field for classroom sessions
        if (selectedType === 'classroom') {
            locationField.style.display = 'block';
        } else {
            locationField.style.display = 'none';
        }

        // Show/hide online fields for online sessions
        if (selectedType === 'online') {
            onlineFields.style.display = 'block';
        } else {
            onlineFields.style.display = 'none';
        }
    }

    sessionTypeSelect.addEventListener('change', toggleFields);

    // Initial check on page load
    toggleFields();
});
</script>
@endsection
