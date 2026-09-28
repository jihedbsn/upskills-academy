@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i data-feather="video" class="me-2"></i>
                        <h4 class="card-title mb-0">Add Past Zoom Meeting Recording</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <i data-feather="alert-circle" class="me-1"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('zoom-meetings.store-recording') }}">
                        @csrf

                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Select a course...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('topic') is-invalid @enderror" id="topic" name="topic" value="{{ old('topic') }}" required placeholder="e.g., Week 1 - Introduction to Course">
                            @error('topic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Date -->
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Meeting Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 60) }}" min="15" max="480" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Recording URL -->
                        <div class="mb-4">
                            <label for="recording_url" class="form-label">
                                <i data-feather="video" class="me-1"></i> Recording URL <span class="text-danger">*</span>
                            </label>
                            <input type="url"
                                   class="form-control @error('recording_url') is-invalid @enderror"
                                   id="recording_url"
                                   name="recording_url"
                                   value="{{ old('recording_url') }}"
                                   placeholder="https://zoom.us/rec/share/..."
                                   required>
                            <small class="form-text text-muted">
                                <i data-feather="link" class="me-1"></i> Paste the Zoom cloud recording link here. Students will see it in their dashboard automatically.
                            </small>
                            @error('recording_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Agenda -->
                        <div class="mb-3">
                            <label for="agenda" class="form-label">Description / Agenda</label>
                            <textarea class="form-control @error('agenda') is-invalid @enderror" id="agenda" name="agenda" rows="3">{{ old('agenda') }}</textarea>
                            @error('agenda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info d-flex align-items-start">
                            <i data-feather="info" class="me-2 mt-1"></i>
                            <div>
                                <strong>Note:</strong> This creates a past meeting record with a recording.
                                No Zoom API call will be made, and no notifications will be sent to students.
                                The recording will appear in their dashboard automatically under "Past Meetings".
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('zoom-meetings.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1"></i> Add Recording
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Feather Icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
