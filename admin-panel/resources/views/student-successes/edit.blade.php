@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Modifier le Succès Étudiant</h4>
                            <p class="text-white-50 mb-0">Modification des informations du succès</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Succès</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student-successes.update', $studentSuccess) }}">
                        @csrf
                        @method('PUT')

                        <!-- Étudiant (lecture seule) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Étudiant</label>
                            <div class="form-control-plaintext bg-light p-3 rounded">
                                <strong>{{ $studentSuccess->student->first_name }} {{ $studentSuccess->student->last_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $studentSuccess->student->email }}</small>
                            </div>
                            <input type="hidden" name="student_id" value="{{ $studentSuccess->student_id }}">
                        </div>

                        <!-- Produit (lecture seule) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Produit</label>
                            <div class="form-control-plaintext bg-light p-3 rounded">
                                <strong>{{ $studentSuccess->product->variation_title }}</strong>
                            </div>
                            <input type="hidden" name="product_id" value="{{ $studentSuccess->product_id }}">
                        </div>

                        <!-- Lien vidéo -->
                        <div class="mb-3">
                            <label for="lien_video" class="form-label">Lien Vidéo</label>
                            <input type="url" class="form-control @error('lien_video') is-invalid @enderror" 
                                   id="lien_video" name="lien_video" 
                                   value="{{ old('lien_video', $studentSuccess->lien_video) }}" 
                                   placeholder="https://example.com/video">
                            @error('lien_video')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="success" class="form-label">Statut</label>
                            <select class="form-select @error('success') is-invalid @enderror" id="success" name="success">
                                <option value="0" {{ old('success', $studentSuccess->success) == 0 ? 'selected' : '' }}>En attente</option>
                                <option value="1" {{ old('success', $studentSuccess->success) == 1 ? 'selected' : '' }}>Approuvé</option>
                                <option value="-1" {{ old('success', $studentSuccess->success) == -1 ? 'selected' : '' }}>Rejeté</option>
                            </select>
                            @error('success')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes admin -->
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Notes de l'administrateur</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      id="admin_notes" name="admin_notes" rows="4" 
                                      placeholder="Notes ou commentaires sur ce succès...">{{ old('admin_notes', $studentSuccess->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save"></i> Enregistrer
                            </button>
                            <a href="{{ route('student-successes.show', $studentSuccess) }}" class="btn btn-outline-secondary">
                                <i data-feather="x"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Historique des Quiz -->
            @if($quizResults->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i data-feather="help-circle" class="me-2"></i>
                        Quiz Passés ({{ $quizResults->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($quizResults as $quizResult)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $quizResult->quiz->title ?? 'Quiz supprimé' }}</h6>
                                    <small class="text-muted">
                                        Score: {{ $quizResult->score }}/{{ $quizResult->total_questions }}
                                        @if($quizResult->total_questions > 0)
                                            ({{ round(($quizResult->score / $quizResult->total_questions) * 100) }}%)
                                        @else
                                            (N/A)
                                        @endif
                                    </small>
                                    <br>
                                    <small class="text-muted">{{ $quizResult->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <span class="badge bg-{{ $quizResult->success ? 'success' : 'danger' }}">
                                    {{ $quizResult->success ? 'Réussi' : 'Échoué' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique des Soumissions de Stage -->
            @if($stageSubmissions->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i data-feather="briefcase" class="me-2"></i>
                        Soumissions de Stage ({{ $stageSubmissions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($stageSubmissions as $stageSubmission)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Soumission de Stage</h6>
                                    <small class="text-muted">
                                        @if($stageSubmission->submitted_at)
                                            Soumis le {{ $stageSubmission->submitted_at->format('d/m/Y H:i') }}
                                        @else
                                            Non soumis
                                        @endif
                                    </small>
                                    @if($stageSubmission->file_path)
                                    <br>
                                    <small class="text-muted">
                                        <i data-feather="file" class="me-1"></i>
                                        Fichier joint
                                    </small>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $stageSubmission->is_valid == 1 ? 'success' : ($stageSubmission->is_valid == -1 ? 'danger' : 'warning') }}">
                                    {{ $stageSubmission->is_valid == 1 ? 'Validé' : ($stageSubmission->is_valid == -1 ? 'Rejeté' : 'En attente') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique des Examens Vidéo -->
            @if($videoExams->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i data-feather="video" class="me-2"></i>
                        Examens Vidéo ({{ $videoExams->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($videoExams as $videoExam)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Examen Vidéo</h6>
                                    <small class="text-muted">
                                        @if($videoExam->submitted_at)
                                            Soumis le {{ $videoExam->submitted_at->format('d/m/Y H:i') }}
                                        @else
                                            Non soumis
                                        @endif
                                    </small>
                                    @if($videoExam->lien_video)
                                    <br>
                                    <small class="text-muted">
                                        <i data-feather="play" class="me-1"></i>
                                        Vidéo disponible
                                    </small>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $videoExam->is_valid == 1 ? 'success' : ($videoExam->is_valid == -1 ? 'danger' : 'warning') }}">
                                    {{ $videoExam->is_valid == 1 ? 'Validé' : ($videoExam->is_valid == -1 ? 'Rejeté' : 'En attente') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Informations -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">ID:</small>
                        <span class="fw-bold">{{ $studentSuccess->id }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Créé le:</small>
                        <span class="fw-bold">{{ $studentSuccess->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Modifié le:</small>
                        <span class="fw-bold">{{ $studentSuccess->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($studentSuccess->submitted_at)
                    <div class="mb-2">
                        <small class="text-muted">Soumis le:</small>
                        <span class="fw-bold">{{ $studentSuccess->submitted_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if($studentSuccess->validated_at)
                    <div class="mb-2">
                        <small class="text-muted">Validé le:</small>
                        <span class="fw-bold">{{ $studentSuccess->validated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student-successes.show', $studentSuccess) }}" class="btn btn-outline-info">
                            <i data-feather="eye"></i> Voir les détails
                        </a>
                        @php
                            $certificateService = new \App\Services\CertificateGeneratorService();
                            $certificate = $certificateService->getCertificate($studentSuccess);
                        @endphp
                        @if($certificate && $certificate->file_path)
                            <a href="{{ route('student-successes.download-certificate', $studentSuccess) }}" class="btn btn-outline-success">
                                <i data-feather="download"></i> Télécharger le certificat
                            </a>
                        @endif
                        <a href="{{ route('student-successes.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="list"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
