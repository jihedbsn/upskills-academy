@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-envelope-open-text"></i> Compose Internal Message</h2>
                <a href="{{ route('internal-messages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Messages
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('internal-messages.store') }}" method="POST" enctype="multipart/form-data" id="messageForm">
                @csrf

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">Message Body <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="body" name="body" rows="10" required>{{ old('body') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="attachments" class="form-label">Attachments (Max 10MB per file)</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <small class="form-text text-muted">You can select multiple files</small>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Select Recipients <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="recipient_type" id="students" value="students" checked>
                        <label class="btn btn-outline-primary" for="students">
                            <i class="fas fa-users"></i> Select Students
                        </label>

                        <input type="radio" class="btn-check" name="recipient_type" id="courses" value="courses">
                        <label class="btn btn-outline-primary" for="courses">
                            <i class="fas fa-graduation-cap"></i> By Course
                        </label>
                    </div>
                </div>

                <!-- Student Selection -->
                <div id="student-selection" class="mb-3">
                    <label for="student-search" class="form-label">Search Students</label>
                    <input type="text" class="form-control mb-2" id="student-search" placeholder="Type to search by name or email...">
                    <div id="student-results" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        <p class="text-muted text-center">Start typing to search for students...</p>
                    </div>
                    <div id="selected-students" class="mt-2"></div>
                </div>

                <!-- Course Selection -->
                <div id="course-selection" class="mb-3" style="display:none;">
                    <label class="form-label">Select Courses</label>
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        @foreach($courses as $course)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="course_ids[]" value="{{ $course->id }}" id="course{{ $course->id }}">
                                <label class="form-check-label" for="course{{ $course->id }}">
                                    {{ $course->titre }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedStudents = new Set();

    // Toggle recipient type
    $('input[name="recipient_type"]').change(function() {
        if ($(this).val() === 'students') {
            $('#student-selection').show();
            $('#course-selection').hide();
        } else {
            $('#student-selection').hide();
            $('#course-selection').show();
        }
    });

    // Student search with debounce
    let searchTimeout;
    $('#student-search').on('input', function() {
        const query = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $('#student-results').html('<p class="text-muted text-center">Type at least 2 characters...</p>');
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("api.search-students") }}',
                data: { q: query },
                success: function(students) {
                    if (students.length === 0) {
                        $('#student-results').html('<p class="text-muted text-center">No students found</p>');
                        return;
                    }

                    let html = '';
                    students.forEach(function(student) {
                        const isSelected = selectedStudents.has(student.id);
                        const btnClass = isSelected ? 'btn-success' : 'btn-outline-primary';
                        const btnText = isSelected ? 'Selected' : 'Select';

                        html += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                <div>
                                    <strong>${student.first_name} ${student.last_name}</strong><br>
                                    <small class="text-muted">${student.email}</small>
                                </div>
                                <button type="button" class="btn btn-sm ${btnClass} select-student"
                                        data-id="${student.id}"
                                        data-name="${student.first_name} ${student.last_name}"
                                        data-email="${student.email}">
                                    ${btnText}
                                </button>
                            </div>
                        `;
                    });
                    $('#student-results').html(html);
                }
            });
        }, 300);
    });

    // Select/deselect student
    $(document).on('click', '.select-student', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');

        if (selectedStudents.has(id)) {
            selectedStudents.delete(id);
            $(this).removeClass('btn-success').addClass('btn-outline-primary').text('Select');
            $(`#selected-badge-${id}`).remove();
        } else {
            selectedStudents.add(id);
            $(this).removeClass('btn-outline-primary').addClass('btn-success').text('Selected');

            $('#selected-students').append(`
                <span class="badge bg-primary me-2 mb-2" id="selected-badge-${id}">
                    ${name}
                    <input type="hidden" name="student_ids[]" value="${id}">
                    <i class="fas fa-times ms-1" style="cursor:pointer" onclick="removeStudent(${id})"></i>
                </span>
            `);
        }

        updateSelectedCount();
    });

    function removeStudent(id) {
        selectedStudents.delete(id);
        $(`#selected-badge-${id}`).remove();
        $(`.select-student[data-id="${id}"]`).removeClass('btn-success').addClass('btn-outline-primary').text('Select');
        updateSelectedCount();
    }

    function updateSelectedCount() {
        if (selectedStudents.size > 0) {
            $('#selected-students').prepend(`
                <div class="alert alert-info" id="selected-count">
                    <i class="fas fa-info-circle"></i> ${selectedStudents.size} student(s) selected
                </div>
            `);
            $('#selected-count').siblings('#selected-count').remove();
        } else {
            $('#selected-count').remove();
        }
    }

    // Form validation
    $('#messageForm').submit(function(e) {
        const recipientType = $('input[name="recipient_type"]:checked').val();

        if (recipientType === 'students' && selectedStudents.size === 0) {
            e.preventDefault();
            alert('Please select at least one student');
            return false;
        }

        if (recipientType === 'courses' && $('input[name="course_ids[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one course');
            return false;
        }
    });
</script>
@endpush
@endsection
