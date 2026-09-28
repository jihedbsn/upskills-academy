@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Détails de l'Examen Vidéo</h1>
        <div class="page-actions">
            <a href="{{ route('student-video-exams.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('student-video-exams.edit', $studentVideoExam) }}" class="btn btn-warning">
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
                        <i data-feather="video" class="me-2"></i>
                        Informations générales
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID de l'examen</label>
                                <p class="form-control-plaintext">{{ $studentVideoExam->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Statut</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $studentVideoExam->status_class }} fs-6">
                                        {{ $studentVideoExam->status_text }}
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
                                    <h6 class="mb-1">{{ $studentVideoExam->student ? $studentVideoExam->student->first_name . ' ' . $studentVideoExam->student->last_name : 'Student not found' }}</h6>
                                    <small class="text-muted">{{ $studentVideoExam->student->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Produit</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $studentVideoExam->product->variation_title }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Lien de la vidéo</label>
                        <div class="form-control-plaintext">
                            <div class="d-flex align-items-center">
                                <a href="{{ $studentVideoExam->lien }}" target="_blank" class="btn btn-primary me-3">
                                    <i data-feather="external-link" class="me-2"></i>
                                    Ouvrir la vidéo
                                </a>
                                <div class="text-truncate" style="max-width: 400px;" title="{{ $studentVideoExam->lien }}">
                                    {{ $studentVideoExam->lien }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description de la vidéo</label>
                        <div class="form-control-plaintext bg-light p-3 rounded">
                            {{ $studentVideoExam->video_description }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de soumission</label>
                                <p class="form-control-plaintext">
                                    {{ $studentVideoExam->submitted_at ? $studentVideoExam->submitted_at->format('d/m/Y à H:i') : 'Non définie' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de création</label>
                                <p class="form-control-plaintext">
                                    {{ $studentVideoExam->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aperçu de la vidéo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="play-circle" class="me-2"></i>
                        Aperçu de la vidéo
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <a href="{{ $studentVideoExam->lien }}" target="_blank" class="btn btn-lg btn-primary">
                                <i data-feather="play" class="me-2"></i>
                                Regarder la vidéo
                            </a>
                        </div>
                        <p class="text-muted">Cliquez sur le bouton ci-dessus pour ouvrir la vidéo dans un nouvel onglet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        @if($studentVideoExam->is_valid == 0)
                            <form method="POST" action="{{ route('student-video-exams.validate', $studentVideoExam) }}"
                                  class="d-inline" onsubmit="return confirm('Valider cet examen vidéo ?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i data-feather="check" class="me-2"></i>
                                    Valider
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectVideoModal">
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

<!-- Rejection Modal for Video Exam -->
@if($studentVideoExam->is_valid == 0)
<div class="modal fade" id="rejectVideoModal" tabindex="-1" aria-labelledby="rejectVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-video-exams.reject', $studentVideoExam) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectVideoModalLabel">
                        <i data-feather="x-circle" class="me-2"></i>
                        Reject Video Exam
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted">
                            You are about to reject the video exam from <strong>{{ $studentVideoExam->student ? $studentVideoExam->student->first_name . ' ' . $studentVideoExam->student->last_name : 'Unknown' }}</strong>
                            for <strong>{{ $studentVideoExam->product->variation_title }}</strong>.
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
