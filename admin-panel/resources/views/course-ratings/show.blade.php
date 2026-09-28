@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.course-ratings.index') }}">Course Ratings</a></li>
                <li class="breadcrumb-item active">Rating #{{ $courseRating->id }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Rating Details Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Rating Details</h4>
                <div>
                    @if($courseRating->is_approved)
                        <span class="badge bg-success fs-6">Approved</span>
                    @else
                        <span class="badge bg-warning fs-6">Pending</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Student Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Student</h6>
                        @if($courseRating->student)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-content bg-primary">{{ $courseRating->student->initials }}</span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $courseRating->student->full_name }}</h5>
                                    <small class="text-muted">{{ $courseRating->student->email }}</small>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Student deleted</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Course</h6>
                        @if($courseRating->product)
                            <span class="badge bg-primary fs-6">{{ $courseRating->product->titre ?? $courseRating->product->name_en ?? 'N/A' }}</span>
                        @else
                            <p class="text-muted">Course deleted</p>
                        @endif
                    </div>
                </div>

                <!-- Rating -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Rating</h6>
                    <div class="d-flex align-items-center">
                        <div class="d-flex me-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i data-feather="star" class="text-warning" style="width: 24px; height: 24px; {{ $i <= $courseRating->rating ? 'fill: #ffc107;' : '' }}"></i>
                            @endfor
                        </div>
                        <span class="fw-bold fs-4">{{ $courseRating->rating }}/5</span>
                    </div>
                </div>

                <!-- Comment -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Student's Comment</h6>
                    <div class="p-3 bg-light rounded">
                        @if($courseRating->commentaire)
                            {{ $courseRating->commentaire }}
                        @else
                            <em class="text-muted">No comment provided</em>
                        @endif
                    </div>
                </div>

                <!-- Admin Response -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Admin Response</h6>
                    @if($courseRating->admin_response)
                        <div class="p-3 bg-info bg-opacity-10 rounded border border-info">
                            {{ $courseRating->admin_response }}
                            <div class="mt-2">
                                <small class="text-muted">Responded on: {{ $courseRating->admin_response_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-light rounded">
                            <em class="text-muted">No response yet</em>
                        </div>
                    @endif
                </div>

                <!-- Respond Form -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">{{ $courseRating->admin_response ? 'Update Response' : 'Add Response' }}</h6>
                    <form id="respondForm">
                        <textarea class="form-control mb-3" id="admin_response" rows="4" placeholder="Enter your response...">{{ $courseRating->admin_response }}</textarea>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send" class="me-2" style="width: 16px; height: 16px;"></i>
                            {{ $courseRating->admin_response ? 'Update Response' : 'Send Response' }}
                        </button>
                    </form>
                </div>

                <!-- Dates -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Created At</h6>
                        <p>{{ $courseRating->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($courseRating->approved_at)
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Approved At</h6>
                        <p>{{ $courseRating->approved_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($courseRating->is_approved)
                        <button class="btn btn-warning" onclick="toggleApproval({{ $courseRating->id }}, false)">
                            <i data-feather="x-circle" class="me-2"></i> Disapprove
                        </button>
                    @else
                        <button class="btn btn-success" onclick="toggleApproval({{ $courseRating->id }}, true)">
                            <i data-feather="check-circle" class="me-2"></i> Approve
                        </button>
                    @endif
                    <a href="{{ route('admin.course-ratings.edit', $courseRating) }}" class="btn btn-outline-primary">
                        <i data-feather="edit" class="me-2"></i> Edit Rating
                    </a>
                    <button class="btn btn-outline-danger" onclick="deleteRating({{ $courseRating->id }})">
                        <i data-feather="trash-2" class="me-2"></i> Delete Rating
                    </button>
                    <a href="{{ route('admin.course-ratings.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="me-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('respondForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const responseText = document.getElementById('admin_response').value.trim();
    if (!responseText) {
        alert('Please enter a response');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    fetch(`/admin/course-ratings/{{ $courseRating->id }}/respond`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ admin_response: responseText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Response saved successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save response'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the response');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        setTimeout(() => feather.replace(), 100);
    });
});

function toggleApproval(ratingId, approved) {
    const action = approved ? 'approve' : 'disapprove';
    if (!confirm(`Are you sure you want to ${action} this rating?`)) {
        return;
    }

    const url = `/admin/course-ratings/${ratingId}/${action}`;

    fetch(url, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status');
        }
    });
}

function deleteRating(ratingId) {
    if (!confirm('Are you sure you want to delete this rating?')) {
        return;
    }

    fetch(`/admin/course-ratings/${ratingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.course-ratings.index") }}';
        } else {
            alert('Error deleting rating');
        }
    });
}
</script>
@endpush
