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
                                <i class="fa fa-trophy text-white" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Succès Étudiants</h4>
                            <p class="text-white-50 mb-0">Gestion des succès et témoignages des étudiants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student-successes.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Rechercher</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nom, email, produit...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="product_id" class="form-label">Produit</label>
                            <select class="form-select" id="product_id" name="product_id">
                                <option value="">Tous les produits</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->variation_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('student-successes.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times"></i> Effacer
                            </a>
                        </div>
                    </form>
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
                            <a href="{{ route('student-successes.by-product') }}" class="btn btn-info">
                                <i class="fa fa-th"></i> Vue par Produit
                            </a>
                        </div>
                        <div>
                            <span class="text-muted">Total: {{ $studentSuccesses->total() }} succès</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des succès -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($studentSuccesses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Produit</th>
                                        <th>Lien Vidéo</th>
                                        <th>Statut</th>
                                        <th>Certificat</th>
                                        <th>Date Soumission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentSuccesses as $studentSuccess)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $studentSuccess->student->first_name }} {{ $studentSuccess->student->last_name }}</h6>
                                                <small class="text-muted">{{ $studentSuccess->student->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ optional($studentSuccess->product)->variation_title ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($studentSuccess->lien_video)
                                                <a href="{{ $studentSuccess->lien_video }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-play"></i> Voir
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $statusColors[$studentSuccess->status] }}">
                                                {{ $statusTexts[$studentSuccess->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $certificateService = new \App\Services\CertificateGeneratorService();
                                                $certificate = $certificateService->getCertificate($studentSuccess);
                                            @endphp
                                            @if($certificate && $certificate->file_path)
                                                <span class="badge bg-success">
                                                    <i class="fa fa-check-circle me-1"></i> Disponible
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $certificate->serial_number }}</small>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fa fa-times-circle me-1"></i> Non généré
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($studentSuccess->submitted_at)
                                                {{ $studentSuccess->submitted_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('student-successes.show', $studentSuccess) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('student-successes.edit', $studentSuccess) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @php
                                                    $certificateService = new \App\Services\CertificateGeneratorService();
                                                    $certificate = $certificateService->getCertificate($studentSuccess);
                                                @endphp
                                                @if($certificate && $certificate->file_path)
                                                    <a href="{{ route('student-successes.download-certificate', $studentSuccess) }}" class="btn btn-sm btn-outline-success" title="Télécharger le certificat">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                @elseif($studentSuccess->success == 1 && optional($studentSuccess->product)->certif_id)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success generate-certificate-btn" 
                                                            title="Générer le certificat"
                                                            data-success-id="{{ $studentSuccess->id }}"
                                                        data-student-name="{{ $studentSuccess->student->first_name }} {{ $studentSuccess->student->last_name }}"
                                                        data-product-name="{{ optional($studentSuccess->product)->variation_title ?? ('Product #' . ($studentSuccess->product_id ?? '')) }}">
                                                        <i class="fa fa-certificate"></i>
                                                    </button>
                                                @elseif($studentSuccess->success == 1 && !optional($studentSuccess->product)->certif_id)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            disabled
                                                            title="Aucun template de certificat associé à ce produit">
                                                        <i class="fa fa-certificate"></i>
                                                    </button>
                                                @elseif(optional($studentSuccess->product)->certif_id)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info test-generate-certificate-btn" 
                                                            title="Générer un certificat de test"
                                                            data-success-id="{{ $studentSuccess->id }}"
                                                        data-student-name="{{ $studentSuccess->student->first_name }} {{ $studentSuccess->student->last_name }}"
                                                        data-product-name="{{ optional($studentSuccess->product)->variation_title ?? ('Product #' . ($studentSuccess->product_id ?? '')) }}">
                                                        <i class="fa fa-flask"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $studentSuccesses->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-trophy text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">Aucun succès trouvé</h5>
                            <p class="text-muted">Aucun succès étudiant ne correspond à vos critères de recherche.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Style pour le bouton de génération de certificat */
.generate-certificate-btn {
    transition: all 0.3s ease;
}

.generate-certificate-btn:hover {
    background-color: #198754 !important;
    color: white !important;
    transform: scale(1.05);
}

.generate-certificate-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Style pour le bouton de génération de certificat de test */
.test-generate-certificate-btn {
    transition: all 0.3s ease;
}

.test-generate-certificate-btn:hover {
    background-color: #0dcaf0 !important;
    color: white !important;
    transform: scale(1.05);
}

.test-generate-certificate-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le clic sur les boutons "Generate Certificate"
    document.querySelectorAll('.generate-certificate-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const successId = this.getAttribute('data-success-id');
            const studentName = this.getAttribute('data-student-name');
            const productName = this.getAttribute('data-product-name');
            
            // Confirmation avant d'exécuter l'action
            if (confirm(`Generate certificate for this student?\n\nStudent: ${studentName}\nProduct: ${productName}\n\nThis will create a certificate with:\n- Student name\n- Current date\n- QR code with serial number\n- Dynamic positioning from template`)) {
                
                // Désactiver le bouton pendant la requête
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                
                // Envoyer la requête AJAX
                fetch(`/student-successes/${successId}/generate-certificate-direct`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher un message de succès
                        alert('✅ ' + data.message + '\n\nSerial Number: ' + data.certificate.serial_number);
                        
                        // Recharger la page pour voir les changements
                        window.location.reload();
                    } else {
                        // Afficher un message d'erreur
                        alert('❌ ' + data.message);
                        
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = '<i class="fa fa-certificate"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ An error occurred while generating the certificate.');
                    
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = '<i class="fa fa-certificate"></i>';
                });
            }
        });
    });
    
    // Gérer le clic sur les boutons "Test Generate Certificate"
    document.querySelectorAll('.test-generate-certificate-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const successId = this.getAttribute('data-success-id');
            const studentName = this.getAttribute('data-student-name');
            const productName = this.getAttribute('data-product-name');
            
            // Confirmation avant d'exécuter l'action
            if (confirm(`Generate TEST certificate for this student?\n\nStudent: ${studentName}\nProduct: ${productName}\n\nThis will create a TEST certificate with:\n- Student name\n- Current date\n- QR code with serial number\n- Dynamic positioning from template\n\nNote: This is a test certificate and will not be saved permanently.`)) {
                
                // Désactiver le bouton pendant la requête
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                
                // Envoyer la requête AJAX pour générer un certificat de test
                fetch(`/student-successes/${successId}/test-generate-certificate`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher un message de succès avec lien de téléchargement
                        alert('✅ ' + data.message + '\n\nSerial Number: ' + data.serial_number + '\n\nYou can download the test certificate using the provided link.');
                        
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = '<i class="fa fa-flask"></i>';
                    } else {
                        // Afficher un message d'erreur
                        alert('❌ ' + data.message);
                        
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = '<i class="fa fa-flask"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ An error occurred while generating the test certificate.');
                    
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = '<i class="fa fa-flask"></i>';
                });
            }
        });
    });
});
</script>

@endsection
