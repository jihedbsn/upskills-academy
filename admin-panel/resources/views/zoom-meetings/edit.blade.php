@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Zoom Meeting</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <i data-feather="alert-circle" class="me-1"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('zoom-meetings.update', $zoomMeeting) }}">
                        @csrf
                        @method('PUT')

                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Select a course...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $zoomMeeting->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->titre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Topic -->
                        <div class="mb-3">
                            <label for="topic" class="form-label">Meeting Topic <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('topic') is-invalid @enderror" id="topic" name="topic" value="{{ old('topic', $zoomMeeting->topic) }}" required>
                            @error('topic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Date & Time -->
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $zoomMeeting->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $zoomMeeting->duration) }}" min="15" max="480" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Timezone -->
                            <div class="col-md-6 mb-3">
                                <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                                    @foreach($timezones as $timezone)
                                        <option value="{{ $timezone }}" {{ old('timezone', $zoomMeeting->timezone) == $timezone ? 'selected' : '' }}>
                                            {{ $timezone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Moderator Email -->
                            <div class="col-md-6 mb-3">
                                <label for="moderator_email" class="form-label">Moderator Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('moderator_email') is-invalid @enderror" id="moderator_email" name="moderator_email" value="{{ old('moderator_email', $zoomMeeting->moderator_email) }}" required>
                                @error('moderator_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $zoomMeeting->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="completed" {{ old('status', $zoomMeeting->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $zoomMeeting->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Agenda -->
                        <div class="mb-3">
                            <label for="agenda" class="form-label">Agenda / Description</label>
                            <textarea class="form-control @error('agenda') is-invalid @enderror" id="agenda" name="agenda" rows="3">{{ old('agenda', $zoomMeeting->agenda) }}</textarea>
                            @error('agenda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Recording URL -->
                        <div class="mb-4">
                            <label for="recording_url" class="form-label">
                                <i data-feather="video" class="me-1"></i> Recording URL
                            </label>
                            <input type="url" class="form-control @error('recording_url') is-invalid @enderror" id="recording_url" name="recording_url" value="{{ old('recording_url', $zoomMeeting->recording_url) }}" placeholder="https://zoom.us/rec/share/...">
                            <small class="form-text text-muted">
                                <i data-feather="info" class="me-1"></i> Add the Zoom recording URL here after the session. Students will see this in their dashboard.
                            </small>
                            @error('recording_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('zoom-meetings.show', $zoomMeeting) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1"></i> Update Meeting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password is now embedded in Zoom join URL automatically

// Initialize Feather Icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
