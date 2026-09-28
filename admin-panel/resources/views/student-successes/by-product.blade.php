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
                                <i data-feather="grid" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Student Successes - Product View</h4>
                            <p class="text-white-50 mb-0">Successes grouped by product for optimized management</p>
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
                    <form method="GET" action="{{ route('student-successes.by-product') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Name, email, product...">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i data-feather="search"></i> Filter
                            </button>
                            <a href="{{ route('student-successes.by-product') }}" class="btn btn-outline-secondary">
                                <i data-feather="x"></i> Clear
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
                            <a href="{{ route('student-successes.index') }}" class="btn btn-info">
                                <i data-feather="list"></i> List View
                            </a>
                        </div>
                        <div>
                            <span class="text-muted">Total: {{ $groupedByProduct->flatten()->count() }} successes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-graduation-cap fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedByProduct->count() }}</h4>
                    <small>Courses with Successes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedByProduct->flatten()->count() }}</h4>
                    <small>Total Successes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-clock fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedByProduct->flatten()->where('success', 0)->count() }}</h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedByProduct->flatten()->where('success', 1)->count() }}</h4>
                    <small>Approved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des succès par cours -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-trophy me-2"></i>
                        Successes by Course
                    </h5>
                </div>
                <div class="card-body">
                    @if($groupedByProduct->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Number of Students</th>
                                        <th>Pending</th>
                                        <th>Approved</th>
                                        <th>Rejected</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedByProduct as $index => $successes)
                                        @php
                                            $product = optional($successes->first())->product;
                                            $pendingCount = $successes->where('success', 0)->count();
                                            $approvedCount = $successes->where('success', 1)->count();
                                            $rejectedCount = $successes->where('success', -1)->count();
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fa fa-graduation-cap text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ optional($product)->variation_title ?? ($product ? ('Course #' . $product->id) : 'Course') }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $product ? $product->id : '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $successes->count() }} student(s)
                                                </span>
                                            </td>
                                            <td>
                                                @if($pendingCount > 0)
                                                    <span class="badge bg-warning">{{ $pendingCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($approvedCount > 0)
                                                    <span class="badge bg-success">{{ $approvedCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rejectedCount > 0)
                                                    <span class="badge bg-danger">{{ $rejectedCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $product && $product->created_at ? $product->created_at->format('d/m/Y') : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($product)
                                                    <a href="{{ route('student-successes.index', ['product_id' => $product->id]) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-eye me-1"></i>
                                                        View Details
                                                    </a>
                                                    @else
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                        <i class="fa fa-eye me-1"></i>
                                                        View Details
                                                    </button>
                                                    @endif
                                                    @if($approvedCount > 0 && optional($product)->certif_id)
                                                        <button type="button"
                                                                class="btn btn-outline-success btn-sm generate-certificates-btn"
                                                                data-product-id="{{ $product ? $product->id : '' }}"
                                                                data-product-name="{{ optional($product)->variation_title ?? ($product->titre ?? ($product ? ('Product #'.$product->id) : 'Product')) }}"
                                                                data-approved-count="{{ $approvedCount }}"
                                                                title="Generate certificates for approved students">
                                                            <i class="fa fa-certificate me-1"></i>
                                                            Generate Certificates
                                                        </button>
                                                    @elseif($approvedCount > 0 && !optional($product)->certif_id)
                                                        <button type="button"
                                                                class="btn btn-outline-secondary btn-sm"
                                                                disabled
                                                                title="No certificate template associated with this product">
                                                            <i class="fa fa-certificate me-1"></i>
                                                            No Template
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-trophy fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No successes found</h5>
                            <p class="text-muted">
                                No student successes match your search criteria.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.card.bg-primary:hover, .card.bg-success:hover, .card.bg-warning:hover, .card.bg-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card.bg-primary .card-body,
.card.bg-success .card-body,
.card.bg-warning .card-body,
.card.bg-info .card-body {
    padding: 1.5rem;
}

.card.bg-primary h4,
.card.bg-success h4,
.card.bg-warning h4,
.card.bg-info h4 {
    font-weight: 600;
    font-size: 1.75rem;
}

.card.bg-primary small,
.card.bg-success small,
.card.bg-warning small,
.card.bg-info small {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* Style pour le bouton de génération de certificats */
.generate-certificates-btn {
    transition: all 0.3s ease;
}

.generate-certificates-btn:hover {
    background-color: #198754 !important;
    color: white !important;
    transform: scale(1.05);
}

.generate-certificates-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le clic sur les boutons "Generate Certificates"
    document.querySelectorAll('.generate-certificates-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const approvedCount = this.getAttribute('data-approved-count');

            // Confirmation avant d'exécuter l'action
            Swal.fire({
                title: 'Generate Certificates',
                html: `
                    <div class="text-start">
                        <p><strong>Product:</strong> ${productName}</p>
                        <p><strong>Approved Students:</strong> ${approvedCount}</p>
                        <p class="text-muted mb-0">This will create certificates for all approved students in this course.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-certificate me-1"></i> Generate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Désactiver le bouton pendant la requête
                    button.disabled = true;
                    button.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Generating...';

                    // Récupérer tous les student successes approuvés pour ce produit
                    fetch(`/student-successes-by-product?product_id=${productId}&status=approved`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.student_successes) {
                            // Générer les certificats pour chaque student success
                            generateCertificatesForProduct(data.student_successes, button, productName);
                        } else {
                            throw new Error('Failed to fetch student successes');
                        }
                    })
                    .catch(error => {
                        button.disabled = false;
                        button.innerHTML = '<i class="fa fa-certificate me-1"></i>Generate Certificates';

                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while fetching student data.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });

    // Fonction pour générer les certificats pour un produit
    function generateCertificatesForProduct(studentSuccesses, button, productName) {
        let completed = 0;
        let errors = 0;
        const total = studentSuccesses.length;

        if (total === 0) {
            button.disabled = false;
            button.innerHTML = '<i class="fa fa-certificate me-1"></i>Generate Certificates';

            Swal.fire({
                title: 'No Students Found',
                text: 'No approved students found for this product.',
                icon: 'info',
                confirmButtonColor: '#17a2b8'
            });
            return;
        }

        // Générer un certificat pour chaque student success
        studentSuccesses.forEach(function(studentSuccess) {
            fetch(`/student-successes/${studentSuccess.id}/generate-certificate-direct`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                completed++;

                if (!data.success) {
                    errors++;
                }

                // Vérifier si toutes les générations sont terminées
                if (completed === total) {
                    // Réactiver le bouton IMMÉDIATEMENT
                    button.disabled = false;
                    button.innerHTML = '<i class="fa fa-certificate me-1"></i>Generate Certificates';

                    if (errors === 0) {
                        Swal.fire({
                            title: 'Success!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2">All certificates have been processed successfully.</p>
                                    <p class="text-muted mb-0"><strong>${total}</strong> certificate(s) generated</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Completed with Errors',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2">Certificate generation completed with some errors.</p>
                                    <p class="text-success mb-0"><strong>${total - errors}</strong> successful</p>
                                    <p class="text-danger mb-0"><strong>${errors}</strong> failed</p>
                                </div>
                            `,
                            icon: 'warning',
                            confirmButtonColor: '#ffc107',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }
            })
            .catch(error => {
                completed++;
                errors++;

                if (completed === total) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa fa-certificate me-1"></i>Generate Certificates';

                    Swal.fire({
                        title: 'Error',
                        html: `
                            <div class="text-center">
                                <p class="mb-2">Certificate generation completed with errors.</p>
                                <p class="text-success mb-0"><strong>${total - errors}</strong> successful</p>
                                <p class="text-danger mb-0"><strong>${errors}</strong> failed</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
});
</script>
