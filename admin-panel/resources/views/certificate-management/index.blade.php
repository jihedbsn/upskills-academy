@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-certificate me-2"></i>
                    Certificate Management
                </h1>
                <p class="text-muted mb-0">Manage generated certificates for approved student successes</p>
            </div>
            <a href="{{ route('certificate-management.create') }}" class="btn btn-success">
                <i class="fa fa-plus me-2"></i>Generate Certificate Manually
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-graduation-cap fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    <small>Total Approved Students</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-certificate fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['with_certificate'] }}</h4>
                    <small>Certificates Generated</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-clock-o fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    <small>Pending Generation</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-filter me-2"></i>
                        Filters
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('certificate-management.index') }}" class="row">
                        <div class="col-md-3">
                            <label for="course_filter" class="form-label">Course</label>
                            <select class="form-select" id="course_filter" name="course">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->titre }} ({{ optional($course->certif)->nom ?? 'No template' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="student_filter" class="form-label">Student</label>
                            <input type="text" class="form-control" id="student_filter" name="student" placeholder="Name or email" value="{{ request('student') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="serial_filter" class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="serial_filter" name="serial_number" placeholder="Serial number" value="{{ request('serial_number') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fa fa-search me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('certificate-management.index') }}" class="btn btn-outline-secondary d-block w-100">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des certificats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="fa fa-list me-2"></i>
                        Certificates ({{ $certificates->total() }})
                    </h4>
                    @if($certificates->count() > 0)
                        <form method="POST" action="{{ route('certificate-management.bulk-generate') }}" id="bulkGenerateForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="student_success_ids" id="bulkStudentIds">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-magic me-1"></i> Generate Selected
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($certificates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Certificate Template</th>
                                        <th>Serial Number</th>
                                        <th>Certificate Date</th>
                                        <th>Generated Date</th>
                                        <th>Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificates as $certificate)
                                        <tr>
                                            <td>
                                                @php
                                                    $hasCert = $certificate->certificates()->exists();
                                                @endphp
                                                @if(!$hasCert)
                                                    <input type="checkbox" class="form-check-input student-checkbox" value="{{ $certificate->id }}">
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($certificate->student->image)
                                                        <img src="{{ $certificate->student->imageUrl }}" alt="{{ $certificate->student->full_name }}" class="rounded-circle me-2" width="32" height="32">
                                                    @else
                                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="fa fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $certificate->student->full_name }}</div>
                                                        <small class="text-muted">{{ $certificate->student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $certificate->product->titre }}</div>
                                                    <small class="text-muted">{{ $certificate->product->variation_title ?? 'No variation' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ optional($certificate->product->certif)->nom ?? 'No template' }}</div>
                                                    <small class="text-muted">{{ optional($certificate->product->certif)->orientation ? ucfirst($certificate->product->certif->orientation) : '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $generatedCert = $certificate->certificates()->first();
                                                @endphp
                                                @if($generatedCert)
                                                    <span class="badge bg-primary">{{ $generatedCert->serial_number }}</span>
                                                @else
                                                    <span class="text-muted">Not generated</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($generatedCert && $generatedCert->certificate_date)
                                                    <div>{{ date('d/m/Y', strtotime($generatedCert->certificate_date)) }}</div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($generatedCert)
                                                    <div>{{ $generatedCert->created_at->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $generatedCert->created_at->format('H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($generatedCert)
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check me-1"></i> Generated
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fa fa-clock-o me-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($generatedCert)
                                                        <a href="{{ route('certificate-management.download', $certificate) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Download Certificate">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editDateModal{{ $certificate->id }}"
                                                                title="Edit Certificate Date">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('certificate-management.delete-generated', $generatedCert) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    title="Delete Certificate"
                                                                    onclick="return confirm('Delete the generated certificate for {{ $certificate->student->full_name }}? The request will be kept.')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('certificate-management.generate', $certificate) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                                    title="Generate Certificate"
                                                                    onclick="return confirm('Generate certificate for {{ $certificate->student->full_name }}?')">
                                                                <i class="fa fa-magic"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('certificate-management.show', $certificate) }}"
                                                       class="btn btn-sm btn-outline-info"
                                                       title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('certificate-management.delete', $certificate->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="Delete Entire Request"
                                                                onclick="return confirm('Delete certificate request for {{ $certificate->student->full_name }}? This will remove the entire record and cannot be undone.')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                <!-- Edit Date Modal -->
                                                @if($generatedCert)
                                                <div class="modal fade" id="editDateModal{{ $certificate->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Certificate Date</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="{{ route('certificate-management.regenerate', $generatedCert) }}" id="regenerateForm{{ $certificate->id }}">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <label for="certificate_date{{ $certificate->id }}" class="form-label">Certificate Date</label>
                                                                        <input type="date" class="form-control" id="certificate_date{{ $certificate->id }}" name="certificate_date"
                                                                               value="{{ $generatedCert->certificate_date ? $generatedCert->certificate_date->format('Y-m-d') : now()->format('Y-m-d') }}" required>
                                                                        <small class="text-muted">This will regenerate the certificate with the new date.</small>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Regenerate certificate with new date?')">
                                                                        <i class="fa fa-refresh me-2"></i> Regenerate Certificate
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-certificate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No certificates found</h5>
                            <p class="text-muted">No approved student successes with certificate templates found.</p>
                        </div>
                    @endif
                </div>
                @if($certificates->hasPages())
                    <div class="card-footer">
                        {{ $certificates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkGenerateForm = document.getElementById('bulkGenerateForm');
    const bulkStudentIds = document.getElementById('bulkStudentIds');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkGenerate();
        });
    }

    // Individual checkbox change
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkGenerate);
    });

    function toggleBulkGenerate() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkGenerateForm.style.display = 'block';
        } else {
            bulkGenerateForm.style.display = 'none';
        }
    }

    // Bulk generate form submission
    if (bulkGenerateForm) {
        bulkGenerateForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            bulkStudentIds.value = JSON.stringify(ids);

            if (ids.length === 0) {
                e.preventDefault();
                alert('Please select at least one student.');
                return false;
            }

            return confirm(`Generate certificates for ${ids.length} selected students?`);
        });
    }
});
</script>
@endpush
@endsection
