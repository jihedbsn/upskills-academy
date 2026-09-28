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
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="trophy" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Détails du Succès Étudiant</h4>
                            <p class="text-white-50 mb-0">Informations complètes sur le succès de l'étudiant</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('student-successes.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('student-successes.edit', $studentSuccess) }}" class="btn btn-warning">
                                <i data-feather="edit"></i> Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails du succès -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Succès</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Étudiant</label>
                                <p class="mb-0">{{ $studentSuccess->student->first_name }} {{ $studentSuccess->student->last_name }}</p>
                                <small class="text-muted">{{ $studentSuccess->student->email }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Produit</label>
                                <p class="mb-0">{{ $studentSuccess->product->variation_title }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Statut</label>
                                <div>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $statusTexts = [
                                            'pending' => 'En attente',
                                            'approved' => 'Approuvé',
                                            'rejected' => 'Rejeté'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$studentSuccess->status] }} fs-6">
                                        {{ $statusTexts[$studentSuccess->status] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de soumission</label>
                                <p class="mb-0">
                                    @if($studentSuccess->submitted_at)
                                        {{ $studentSuccess->submitted_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Non soumis</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($studentSuccess->validated_at)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de validation</label>
                                <p class="mb-0">{{ $studentSuccess->validated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($studentSuccess->lien_video)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lien Vidéo</label>
                        <div>
                            <a href="{{ $studentSuccess->lien_video }}" target="_blank" class="btn btn-primary">
                                <i data-feather="play"></i> Voir la vidéo
                            </a>
                        </div>
                        <small class="text-muted">{{ $studentSuccess->lien_video }}</small>
                    </div>
                    @endif

                    @php
                        $certificateService = new \App\Services\CertificateGeneratorService();
                        $certificate = $certificateService->getCertificate($studentSuccess);
                    @endphp

                    @if($certificate && $certificate->file_path)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Certificat</label>
                        <div>
                            <a href="{{ route('student-successes.download-certificate', $studentSuccess) }}" class="btn btn-success">
                                <i data-feather="download"></i> Télécharger le certificat
                            </a>
                        </div>
                        <small class="text-muted">Numéro de série: {{ $certificate->serial_number }}</small>
                        <br>
                        <small class="text-muted">Généré le: {{ $certificate->generated_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @else
                    <div class="mb-3">
                        <label class="form-label fw-bold">Certificat</label>
                        <div>
                            @if($studentSuccess->product->certif_id)
                                <a href="{{ route('student-successes.test-generate-certificate', $studentSuccess) }}" class="btn btn-warning" onclick="return confirm('Voulez-vous générer un certificat de test pour cet étudiant ?')">
                                    <i data-feather="file-plus"></i> Tester la génération
                                </a>
                                <small class="text-muted d-block mt-1">Certificat associé au produit (ID: {{ $studentSuccess->product->certif_id }})</small>
                                <small class="text-info d-block mt-1">
                                    <i data-feather="info"></i> 
                                    Ce bouton génère un certificat de test avec les positions exactes définies dans le template
                                </small>
                            @else
                                <span class="text-muted">Aucun certificat associé à ce produit</span>
                                <br>
                                <small class="text-muted">Pour générer un certificat, associez d'abord un certificat au produit</small>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($studentSuccess->admin_notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes de l'administrateur</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $studentSuccess->admin_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    @if($studentSuccess->isPending())
                        <form method="POST" action="{{ route('student-successes.validate', $studentSuccess) }}" class="mb-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success w-100">
                                <i data-feather="check"></i> Approuver
                            </button>
                        </form>
                        <form method="POST" action="{{ route('student-successes.reject', $studentSuccess) }}" class="mb-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger w-100">
                                <i data-feather="x"></i> Rejeter
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <p class="text-muted">Ce succès a déjà été traité</p>
                            <a href="{{ route('student-successes.edit', $studentSuccess) }}" class="btn btn-warning mb-2">
                                <i data-feather="edit"></i> Modifier le statut
                            </a>
                        </div>
                    @endif
                    
                    @if($studentSuccess->product->certif_id)
                        <hr>
                        <a href="{{ route('student-successes.test-generate-certificate', $studentSuccess) }}" 
                           class="btn btn-info w-100" 
                           onclick="return confirm('Voulez-vous générer un certificat de test ?')">
                            <i data-feather="file-plus"></i> Tester Génération
                        </a>
                        <small class="text-muted d-block mt-1 text-center">
                            Génère un certificat avec les positions exactes du template
                        </small>
                    @endif
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card mt-3">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
