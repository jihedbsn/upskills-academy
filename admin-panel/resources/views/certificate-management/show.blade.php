@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('certificate-management.index') }}">Certificate Management</a></li>
            <li class="breadcrumb-item active">Certificate Details</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa fa-certificate me-2"></i>
            Certificate Details
        </h1>
        <div class="page-actions">
            <a href="{{ route('certificate-management.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Student & Course Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-user me-2"></i>
                        Student Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        @if($studentSuccess->student->image)
                            <img src="{{ $studentSuccess->student->imageUrl }}" alt="{{ $studentSuccess->student->full_name }}" class="rounded-circle me-3" width="64" height="64">
                        @else
                            <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="fa fa-user fa-2x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $studentSuccess->student->full_name }}</h5>
                            <p class="text-muted mb-1">{{ $studentSuccess->student->email }}</p>
                            <p class="text-muted mb-0">{{ $studentSuccess->student->phone ?? 'No phone' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Country:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $studentSuccess->student->country ?? 'Not specified' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Registration:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $studentSuccess->student->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course & Certificate Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-book me-2"></i>
                        Course & Certificate Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Course Details</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="fw-medium">{{ $studentSuccess->product->titre }}</div>
                            @if($studentSuccess->product->variation_title)
                                <small class="text-muted">{{ $studentSuccess->product->variation_title }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Certificate Template</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="fw-medium">{{ optional($studentSuccess->product->certif)->nom ?? 'No template' }}</div>
                            <small class="text-muted">Orientation: {{ optional($studentSuccess->product->certif)->orientation ? ucfirst($studentSuccess->product->certif->orientation) : '-' }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Success Details</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Status:</strong><br>
                                    @if($studentSuccess->success == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($studentSuccess->success == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <strong>Validated:</strong><br>
                                    {{ $studentSuccess->validated_at ? $studentSuccess->validated_at->format('d/m/Y H:i') : 'Not validated' }}
                                </div>
                            </div>
                            @if($studentSuccess->admin_notes)
                                <div class="mt-2">
                                    <strong>Notes:</strong><br>
                                    <small>{{ $studentSuccess->admin_notes }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Status & Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-certificate me-2"></i>
                        Certificate Status
                    </h4>
                </div>
                <div class="card-body">
                    @if($certificate)
                        <!-- Certificate Exists -->
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1">Certificate Generated</h5>
                                    <p class="mb-0">Serial Number: <strong>{{ $certificate->serial_number }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6>Certificate Details</h6>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <strong>Serial Number:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <code>{{ $certificate->serial_number }}</code>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-4">
                                            <strong>Generated:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificate->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-4">
                                            <strong>Certificate Date:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificate->certificate_date ? $certificate->certificate_date->format('d/m/Y') : 'Not set' }}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-4">
                                            <strong>File Size:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @php
                                                $filePath = public_path($certificate->file_path);
                                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                            @endphp
                                            {{ $fileSize > 0 ? number_format($fileSize / 1024, 2) . ' KB' : 'Unknown' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6>Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('certificate-management.download', $studentSuccess) }}" class="btn btn-primary">
                                            <i class="fa fa-download me-2"></i> Download Certificate
                                        </a>
                                        @if(file_exists(public_path($certificate->file_path)))
                                            <a href="{{ asset($certificate->file_path) }}" target="_blank" class="btn btn-outline-info">
                                                <i class="fa fa-eye me-2"></i> Preview Certificate
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#updateDateModal">
                                            <i class="fa fa-calendar me-2"></i> Change Certificate Date
                                        </button>
                                        <form method="POST" action="{{ route('certificate-management.delete-generated', $certificate) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100"
                                                    onclick="return confirm('Delete this certificate? The student success record will be kept and you can generate a new certificate.')">
                                                <i class="fa fa-trash me-2"></i> Delete Certificate
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Date Modal -->
                        <div class="modal fade" id="updateDateModal" tabindex="-1" aria-labelledby="updateDateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateDateModalLabel">
                                            <i class="fa fa-calendar me-2"></i>
                                            Change Certificate Date
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Current Date:</strong> {{ $certificate->certificate_date ? $certificate->certificate_date->format('d/m/Y') : 'Not set' }}
                                        </div>
                                        <p class="text-muted">Choose how to update the certificate date:</p>

                                        <!-- Update Date Only Form -->
                                        <form method="POST" action="{{ route('certificate-management.update-date', $certificate) }}" class="mb-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="certificate_date" class="form-label">New Certificate Date</label>
                                                <input type="date" class="form-control" id="certificate_date" name="certificate_date"
                                                       value="{{ $certificate->certificate_date ? $certificate->certificate_date->format('Y-m-d') : now()->format('Y-m-d') }}" required>
                                                <small class="text-muted">This will only update the date in the database without regenerating the certificate file.</small>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fa fa-save me-2"></i> Update Date Only
                                            </button>
                                        </form>

                                        <hr>

                                        <!-- Regenerate with New Date Form -->
                                        <form method="POST" action="{{ route('certificate-management.regenerate', $certificate) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="regenerate_certificate_date" class="form-label">New Certificate Date</label>
                                                <input type="date" class="form-control" id="regenerate_certificate_date" name="certificate_date"
                                                       value="{{ $certificate->certificate_date ? $certificate->certificate_date->format('Y-m-d') : now()->format('Y-m-d') }}" required>
                                                <small class="text-muted">This will regenerate the entire certificate with the new date.</small>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('This will regenerate the certificate file with the new date. Continue?')">
                                                <i class="fa fa-refresh me-2"></i> Regenerate Certificate with New Date
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Certificate -->
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-exclamation-triangle fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1">Certificate Not Generated</h5>
                                    <p class="mb-0">This student success has not been issued a certificate yet.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6>Generate Certificate</h6>
                                    <p class="text-muted">Generate a certificate for this approved student success.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6>Actions</h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateCertificateModal">
                                            <i class="fa fa-magic me-2"></i> Generate Certificate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Certificate Modal -->
                        <div class="modal fade" id="generateCertificateModal" tabindex="-1" aria-labelledby="generateCertificateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="generateCertificateModalLabel">
                                            <i class="fa fa-magic me-2"></i>
                                            Generate Certificate
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('certificate-management.generate', $studentSuccess) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle me-2"></i>
                                                <strong>Student:</strong> {{ $studentSuccess->student->full_name }}
                                            </div>
                                            <div class="mb-3">
                                                <label for="generate_certificate_date" class="form-label">Certificate Date</label>
                                                <input type="date" class="form-control" id="generate_certificate_date" name="certificate_date"
                                                       value="{{ now()->format('Y-m-d') }}">
                                                <small class="text-muted">The date that will be displayed on the certificate. Leave blank for today's date.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-magic me-2"></i> Generate Certificate
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
