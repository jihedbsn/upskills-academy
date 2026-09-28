@extends('layouts.app')

@section('title', 'Course Details - ' . ($course->titre ?? 'Course #' . $course->id))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Course header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">
                                <i class="fa fa-graduation-cap me-2"></i>
                                {{ $course->titre ?? 'Course #' . $course->id }}
                            </h4>
                            <p class="card-subtitle text-muted mb-0">
                                Course details and enrolled students list
                            </p>
                        </div>
                        <a href="{{ route('course-students.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Back to list
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Course ID:</strong> {{ $course->id }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Price:</strong>
                                    @if($course->prix > 0)
                                        <span class="badge bg-success">
                                            ${{ number_format($course->prix, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg_info">Free</span>
                                    @endif
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <strong>Created at:</strong> {{ $course->created_at->format('Y-m-d H:i') }}
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <strong>Students count:</strong>
                                    <span class="badge bg-primary">{{ $students->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-primary fs-6 mb-2">
                                    {{ $students->count() }} enrolled student(s)
                                </span>
                                @if($course->type_course)
                                    <span class="badge bg-secondary">
                                        {{ $course->getCourseTypeLabelAttribute() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

                        <!-- Students list -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-users me-2"></i>
                            Enrolled Students
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fa fa-plus me-1"></i>
                            Add Student
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @php $blockedCount = $blockedStudentIds->count(); @endphp
                    @if($blockedCount > 0)
                        <div class="alert alert-danger d-flex align-items-center mb-3 blocked-summary-alert" role="alert">
                            <i class="fa fa-ban fa-lg me-3"></i>
                            <div>
                                <strong>{{ $blockedCount }} student(s) blocked</strong> on this course.
                                Blocked rows are highlighted in red — these students cannot access this course.
                            </div>
                        </div>
                    @endif
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Full name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Registered at</th>
                                        <th>Payment method</th>
                                        <th>Price paid</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $order)
                                        @if($order->student)
                                        @php $isBlocked = isset($blockedStudentIds[$order->student->id]); @endphp
                                        <tr class="{{ $isBlocked ? 'student-row-blocked' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm {{ $isBlocked ? 'bg-danger' : 'bg-primary' }} rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fa {{ $isBlocked ? 'fa-ban' : 'fa-user' }} text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="{{ $isBlocked ? 'text-danger' : '' }}">
                                                            {{ $order->student->first_name }} {{ $order->student->last_name }}
                                                        </strong>
                                                        @if($isBlocked)
                                                            <small class="d-block blocked-row-label">
                                                                <i class="fa fa-exclamation-circle me-1"></i>
                                                                Blocked from this course
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $order->student->email }}" class="text-decoration-none">
                                                    {{ $order->student->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($order->student->phone)
                                                    <a href="tel:{{ $order->student->phone }}" class="text-decoration-none">
                                                        {{ $order->student->phone }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $order->created_at->format('Y-m-d') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($order->payment_method ?? 'Not specified') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($order->price > 0)
                                                    <span class="badge bg-success">
                                                        ${{ number_format($order->price, 2) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isBlocked)
                                                    <span class="badge bg-danger blocked-status-badge">
                                                        <i class="fa fa-lock me-1"></i>
                                                        Blocked
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check-circle me-1"></i>
                                                        Active
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('students.show', $order->student->id) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       target="_blank"
                                                       title="View student details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('course-students.toggle-block', [$course->id, $order->student->id]) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        @if($isBlocked)
                                                            <button type="submit"
                                                                    class="btn btn-outline-success btn-sm"
                                                                    title="Unblock student"
                                                                    onclick="return confirm('Are you sure you want to unblock {{ $order->student->first_name }} {{ $order->student->last_name }} for this course?')">
                                                                <i class="fa fa-unlock"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                    class="btn btn-outline-warning btn-sm"
                                                                    title="Block student"
                                                                    onclick="return confirm('Are you sure you want to block {{ $order->student->first_name }} {{ $order->student->last_name }} from this course?')">
                                                                <i class="fa fa-ban"></i>
                                                            </button>
                                                        @endif
                                                    </form>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="confirmRemoveEnrollment({{ $order->id }}, '{{ $order->student->first_name }} {{ $order->student->last_name }}')"
                                                            title="Remove enrollment">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Students stats -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fa fa-pie-chart me-2"></i>
                                            Enrollment Statistics
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="border-end">
                                                    <h5 class="text-primary mb-1">{{ $students->count() }}</h5>
                                                    <small class="text-muted">Total students</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border-end">
                                                    <h5 class="text-success mb-1">{{ $students->where('price', '>', 0)->count() }}</h5>
                                                    <small class="text-muted">Paid enrollments</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border-end">
                                                    <h5 class="text-info mb-1">{{ $students->where('price', 0)->count() }}</h5>
                                                    <small class="text-muted">Free enrollments</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="text-warning mb-1">${{ number_format($students->sum('price'), 2) }}</h5>
                                                <small class="text-muted">Total revenue</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No enrolled students</h5>
                            <p class="text-muted">
                                This course currently has no enrolled students.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.student-row-blocked {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.12) 0%, rgba(220, 53, 69, 0.04) 100%) !important;
    border-left: 4px solid #dc3545;
    box-shadow: inset 0 0 0 1px rgba(220, 53, 69, 0.15);
}

.student-row-blocked:hover {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.18) 0%, rgba(220, 53, 69, 0.06) 100%) !important;
}

.student-row-blocked td {
    opacity: 0.92;
}

.student-row-blocked .blocked-row-label {
    color: #dc3545;
    font-weight: 600;
    font-size: 0.75rem;
    margin-top: 2px;
}

.blocked-status-badge {
    font-size: 0.8rem;
    padding: 0.45em 0.75em;
    letter-spacing: 0.02em;
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.25);
}

.blocked-summary-alert {
    border-left: 4px solid #dc3545;
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.03) 100%);
    border-color: rgba(220, 53, 69, 0.3);
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

/* Style pour le bouton eye avec effet hover */
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}
</style>

<!-- Hidden form for deleting enrollment -->
<form id="deleteEnrollmentForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">
                    <i class="fa fa-user-plus me-2"></i>
                    Add Student to Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStudentForm" method="POST" action="{{ route('course-students.add-student', $course->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Loading students...</option>
                        </select>
                        <small class="text-muted">Only students not already enrolled are shown</small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Note:</strong> The student will be enrolled for free and will receive an email notification.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check me-1"></i>
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmRemoveEnrollment(orderId, studentName) {
    if (confirm(`Are you sure you want to remove ${studentName}'s enrollment from this course?\n\nThis action will:\n- Remove the student's access to the course\n- Cannot be undone\n\nClick OK to confirm.`)) {
        const form = document.getElementById('deleteEnrollmentForm');
        form.action = `/course-students/${orderId}/remove`;
        form.submit();
    }
}

// Load available students when modal opens
document.addEventListener('DOMContentLoaded', function() {
    const addStudentModal = document.getElementById('addStudentModal');
    const studentSelect = document.getElementById('student_id');

    if (addStudentModal) {
        addStudentModal.addEventListener('show.bs.modal', function() {
            // Reset select
            studentSelect.innerHTML = '<option value="">Loading students...</option>';
            studentSelect.disabled = true;

            // Fetch available students
            fetch('{{ route("course-students.available-students", $course->id) }}')
                .then(response => response.json())
                .then(data => {
                    studentSelect.disabled = false;

                    if (data.students && data.students.length > 0) {
                        studentSelect.innerHTML = '<option value="">-- Select a student --</option>';
                        data.students.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = `${student.name} (${student.email})`;
                            studentSelect.appendChild(option);
                        });
                    } else {
                        studentSelect.innerHTML = '<option value="">No available students</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentSelect.disabled = false;
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        });
    }
});
</script>
@endsection
