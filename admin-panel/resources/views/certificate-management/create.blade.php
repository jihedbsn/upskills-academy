@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-certificate me-2"></i>
                    Generate Certificate Manually
                </h1>
                <p class="text-muted">Select a student and course to generate a certificate</p>
            </div>
            <a href="{{ route('certificate-management.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa fa-file-text-o me-2"></i>
                        Certificate Generation Form
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('certificate-management.manual-generate') }}" method="POST" id="certificateForm">
                        @csrf

                        <!-- Course Selection -->
                        <div class="mb-4">
                            <label for="course_id" class="form-label">
                                <i class="fa fa-book me-1"></i>
                                Select Course <span class="text-danger">*</span>
                            </label>
                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">-- Select a Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-certif="{{ $course->certif->nom ?? 'N/A' }}">
                                        {{ $course->id }} -
                                        @if(isset($course->variations) && $course->variations->isNotEmpty())
                                            {{ $course->variations->first()->name ?? 'Untitled Course' }}
                                        @else
                                            Course #{{ $course->id }}
                                        @endif
                                        (Certificate: {{ $course->certif->nom ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle"></i>
                                Only courses with certificate templates are shown
                            </small>
                        </div>

                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label for="student_id" class="form-label">
                                <i class="fa fa-user me-1"></i>
                                Select Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required disabled>
                                <option value="">-- First select a course --</option>
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="studentLoader" class="text-center mt-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">Loading students...</span>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle"></i>
                                Only students enrolled in the selected course will appear
                            </small>
                        </div>

                        <!-- Certificate Date -->
                        <div class="mb-4">
                            <label for="certificate_date" class="form-label">
                                <i class="fa fa-calendar me-1"></i>
                                Certificate Date (Optional)
                            </label>
                            <input type="date" name="certificate_date" id="certificate_date" class="form-control @error('certificate_date') is-invalid @enderror" value="{{ date('Y-m-d') }}">
                            @error('certificate_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle"></i>
                                Leave empty to use today's date
                            </small>
                        </div>

                        <!-- Alert Box -->
                        <div id="certificateWarning" class="alert alert-warning d-none" role="alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This student already has a certificate for this course.
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('certificate-management.index') }}" class="btn btn-light">
                                <i class="fa fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fa fa-certificate me-2"></i>Generate Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4 border-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="fa fa-info-circle me-2"></i>
                        How it works
                    </h6>
                    <ul class="mb-0">
                        <li>Select a course that has a certificate template configured</li>
                        <li>Choose a student who is enrolled in that course</li>
                        <li>Optionally set a custom certificate date (defaults to today)</li>
                        <li>The system will automatically create a StudentSuccess record if needed</li>
                        <li>A certificate will be generated and emailed to the student</li>
                        <li>If a certificate already exists, you'll be notified</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // When course is selected, load students
    $('#course_id').on('change', function() {
        const courseId = $(this).val();
        const studentSelect = $('#student_id');
        const loader = $('#studentLoader');
        const warning = $('#certificateWarning');

        // Reset student dropdown
        studentSelect.html('<option value="">-- Select a student --</option>').prop('disabled', true);
        warning.addClass('d-none');

        if (!courseId) {
            return;
        }

        // Show loader
        loader.show();

        // Fetch students for this course
        $.ajax({
            url: `/certificate-management/get-students/${courseId}`,
            type: 'GET',
            success: function(response) {
                loader.hide();

                if (response.success && response.students.length > 0) {
                    studentSelect.prop('disabled', false);

                    response.students.forEach(function(student) {
                        let option = `<option value="${student.id}" data-has-cert="${student.has_certificate}">
                            ${student.name} (${student.email})${student.has_certificate ? ' ✓ Has Certificate' : ''}
                        </option>`;
                        studentSelect.append(option);
                    });
                } else {
                    studentSelect.html('<option value="">-- No enrolled students found --</option>');
                }
            },
            error: function(xhr) {
                loader.hide();
                alert('Error loading students. Please try again.');
                console.error('Error:', xhr);
            }
        });
    });

    // When student is selected, check if they have a certificate
    $('#student_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const hasCert = selectedOption.data('has-cert');
        const warning = $('#certificateWarning');

        if (hasCert) {
            warning.removeClass('d-none');
        } else {
            warning.addClass('d-none');
        }
    });

    // Form validation
    $('#certificateForm').on('submit', function(e) {
        const courseId = $('#course_id').val();
        const studentId = $('#student_id').val();

        if (!courseId || !studentId) {
            e.preventDefault();
            alert('Please select both course and student');
            return false;
        }

        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Generating...');
    });
});
</script>
@endpush

<style>
.card {
    border-radius: 8px;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
}

.form-select:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}

.alert {
    border-radius: 6px;
}

.page-header {
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

#certificateWarning {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endsection
