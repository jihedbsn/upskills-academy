@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Détails de la Soumission de Stage</h1>
        <div class="page-actions">
            <a href="{{ route('student-stage-courses.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('student-stage-courses.edit', $studentStageCourse) }}" class="btn btn-warning">
                <i data-feather="edit" class="me-2"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="file-text" class="me-2"></i>
                        Informations générales
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID de la soumission</label>
                                <p class="form-control-plaintext">{{ $studentStageCourse->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Statut</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $studentStageCourse->status_class }} fs-6">
                                        {{ $studentStageCourse->status_text }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Étudiant</label>
                                <div class="form-control-plaintext">
                                    <h6 class="mb-1">{{ $studentStageCourse->student ? $studentStageCourse->student->first_name . ' ' . $studentStageCourse->student->last_name : 'Student not found' }}</h6>
                                    <small class="text-muted">{{ $studentStageCourse->student ? $studentStageCourse->student->email : 'Email not found' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Produit</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $studentStageCourse->product->variation_title }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div class="form-control-plaintext bg-light p-3 rounded">
                            {{ $studentStageCourse->description }}
                        </div>
                    </div>

                    @if($studentStageCourse->admin_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes de l'administrateur</label>
                            <div class="form-control-plaintext bg-warning bg-opacity-10 p-3 rounded">
                                {{ $studentStageCourse->admin_notes }}
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de soumission</label>
                                <p class="form-control-plaintext">
                                    {{ $studentStageCourse->submitted_at ? $studentStageCourse->submitted_at->format('d/m/Y à H:i') : 'Non définie' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de validation</label>
                                <p class="form-control-plaintext">
                                    {{ $studentStageCourse->validated_at ? $studentStageCourse->validated_at->format('d/m/Y à H:i') : 'Non validée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fichiers -->
    @if($studentStageCourse->file1 || $studentStageCourse->file2)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i data-feather="paperclip" class="me-2"></i>
                            Fichiers joints
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($studentStageCourse->file1)
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($studentStageCourse->file1_type === 'image')
                                                        <i data-feather="image" class="text-primary" style="width: 24px; height: 24px;"></i>
                                                    @elseif($studentStageCourse->file1_type === 'pdf')
                                                        <i data-feather="file-text" class="text-danger" style="width: 24px; height: 24px;"></i>
                                                    @else
                                                        <i data-feather="file" class="text-secondary" style="width: 24px; height: 24px;"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Fichier 1</h6>
                                                    <small class="text-muted">{{ $studentStageCourse->file1 }}</small>
                                                </div>
                                                <div>
                                                    <a href="{{ route('student-stage-courses.download-file', [$studentStageCourse, 1]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i data-feather="download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($studentStageCourse->file2)
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($studentStageCourse->file2_type === 'image')
                                                        <i data-feather="image" class="text-primary" style="width: 24px; height: 24px;"></i>
                                                    @elseif($studentStageCourse->file2_type === 'pdf')
                                                        <i data-feather="file-text" class="text-danger" style="width: 24px; height: 24px;"></i>
                                                    @else
                                                        <i data-feather="file" class="text-secondary" style="width: 24px; height: 24px;"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Fichier 2</h6>
                                                    <small class="text-muted">{{ $studentStageCourse->file2 }}</small>
                                                </div>
                                                <div>
                                                    <a href="{{ route('student-stage-courses.download-file', [$studentStageCourse, 2]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i data-feather="download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="settings" class="me-2"></i>
                        Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        @if($studentStageCourse->is_valid == 0)
                            <form method="POST" action="{{ route('student-stage-courses.validate', $studentStageCourse) }}"
                                  class="d-inline" onsubmit="return confirm('Valider cette soumission ?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i data-feather="check" class="me-2"></i>
                                    Valider
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i data-feather="x" class="me-2"></i>
                                Rejeter
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
@if($studentStageCourse->is_valid == 0)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-stage-courses.reject', $studentStageCourse) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i data-feather="x-circle" class="me-2"></i>
                        Reject Submission
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted">
                            You are about to reject the submission from <strong>{{ $studentStageCourse->student ? $studentStageCourse->student->first_name . ' ' . $studentStageCourse->student->last_name : 'Unknown' }}</strong>
                            for <strong>{{ $studentStageCourse->product->variation_title }}</strong>.
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            <strong>Rejection Reason <span class="text-danger">*</span></strong>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason"
                                  class="form-control" rows="4" required minlength="10" maxlength="1000"
                                  placeholder="Please provide a detailed reason for rejection. This will be sent to the student via email so they know what to improve for resubmission."></textarea>
                        <div class="form-text">Minimum 10 characters. This message will be sent to the student.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i data-feather="x" class="me-1"></i>
                        Reject & Notify Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
