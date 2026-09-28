@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Course Rating</h4>
                <div class="card-actions">
                    <a href="{{ route('admin.course-ratings.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.course-ratings.update', $courseRating) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ $courseRating->student_id == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Course <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select Course</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $courseRating->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->titre ?? $product->name_en ?? $product->name_ar ?? 'Course #' . $product->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                <select name="rating" id="rating" class="form-select" required>
                                    <option value="">Select Rating</option>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ $courseRating->rating == $i ? 'selected' : '' }}>
                                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_approved" class="form-label">Status</label>
                                <select name="is_approved" id="is_approved" class="form-select">
                                    <option value="1" {{ $courseRating->is_approved ? 'selected' : '' }}>Approved</option>
                                    <option value="0" {{ !$courseRating->is_approved ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Comment</label>
                        <textarea name="commentaire" id="commentaire" rows="4" class="form-control" placeholder="Optional comment...">{{ old('commentaire', $courseRating->commentaire) }}</textarea>
                        @error('commentaire')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum 1000 characters</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> Update Rating
                        </button>
                        <a href="{{ route('admin.course-ratings.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rating Preview -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Current Rating Preview</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-content">{{ $courseRating->student ? $courseRating->student->initials : '?' }}</span>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $courseRating->student ? $courseRating->student->full_name : 'Unknown Student' }}</h6>
                        <small class="text-muted">{{ $courseRating->product ? ($courseRating->product->titre ?? $courseRating->product->name_en ?? $courseRating->product->name_ar ?? 'Unknown Course') : 'Unknown Course' }}</small>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-2">
                    <strong class="me-2">Rating:</strong>
                    <div class="d-flex me-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-feather="star" class="text-warning" style="width: 20px; height: 20px; {{ $i <= $courseRating->rating ? 'fill: #ffc107;' : '' }}"></i>
                        @endfor
                    </div>
                    <span class="fw-bold">{{ $courseRating->rating }}/5</span>
                </div>

                @if($courseRating->commentaire)
                    <div class="mt-3">
                        <strong class="d-block mb-2">Comment:</strong>
                        <div class="p-3 bg-light rounded">
                            {{ $courseRating->commentaire }}
                        </div>
                    </div>
                @else
                    <div class="mt-3">
                        <em class="text-muted">No comment provided</em>
                    </div>
                @endif

                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Status:</strong>
                        @if($courseRating->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </small>
                    <br>
                    <small class="text-muted">
                        <strong>Created:</strong> {{ $courseRating->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize feather icons
    feather.replace();
</script>
@endpush
