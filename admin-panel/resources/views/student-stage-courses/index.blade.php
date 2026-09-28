@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Soumissions de Stage</h1>
        <div class="page-actions">
            <a href="{{ route('student-stage-courses.by-product') }}" class="btn btn-info">
                <i data-feather="layers" class="me-2"></i>
                Vue par Produit
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="filter" class="me-2"></i>
                        Filtres
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('student-stage-courses.index') }}" class="row">
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Étudiant, produit, description...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Étudiant</label>
                            <select name="student_id" class="form-select">
                                <option value="">Tous les étudiants</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Produit</label>
                            <select name="product_id" class="form-select">
                                <option value="">Tous les produits</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->variation_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>En attente</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Validé</option>
                                <option value="-1" {{ request('status') == '-1' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i data-feather="search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des soumissions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="list" class="me-2"></i>
                        Liste des Soumissions de Stage
                    </h4>
                </div>
                <div class="card-body">
                    @if($studentStageCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Étudiant</th>
                                        <th>Produit</th>
                                        <th>Description</th>
                                        <th>Fichiers</th>
                                        <th>Statut</th>
                                        <th>Date de soumission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentStageCourses as $submission)
                                        <tr>
                                            <td>{{ $submission->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $submission->student ? $submission->student->first_name . ' ' . $submission->student->last_name : 'Student not found' }}</h6>
                                                        <small class="text-muted">{{ $submission->student ? $submission->student->email : 'Email not found' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $submission->product ? $submission->product->variation_title : 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $submission->description }}">
                                                    {{ $submission->description }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($submission->file1)
                                                        <a href="{{ route('student-stage-courses.download-file', [$submission, 1]) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Télécharger fichier 1">
                                                            <i data-feather="download"></i>
                                                        </a>
                                                    @endif
                                                    @if($submission->file2)
                                                        <a href="{{ route('student-stage-courses.download-file', [$submission, 2]) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Télécharger fichier 2">
                                                            <i data-feather="download"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $submission->status_class }}">
                                                    {{ $submission->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('student-stage-courses.show', $submission) }}"
                                                       class="btn btn-sm btn-outline-info" title="Voir">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                    <a href="{{ route('student-stage-courses.edit', $submission) }}"
                                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                    @if($submission->is_valid == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-success" title="Valider"
                                                                data-bs-toggle="modal" data-bs-target="#validateModal{{ $submission->id }}">
                                                            <i data-feather="check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Rejeter"
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $submission->id }}">
                                                            <i data-feather="x"></i>
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
                        <div class="d-flex justify-content-center mt-3">
                            {{ $studentStageCourses->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="inbox" class="feather-48 text-muted"></i>
                            <h4 class="mt-3 text-muted">Aucune soumission trouvée</h4>
                            <p class="text-muted">Il n'y a pas de soumissions de stage correspondant à vos critères.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Modals -->
@foreach($studentStageCourses as $submission)
    @if($submission->is_valid == 0)
    <div class="modal fade" id="validateModal{{ $submission->id }}" tabindex="-1" aria-labelledby="validateModalLabel{{ $submission->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('student-stage-courses.validate', $submission) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="validateModalLabel{{ $submission->id }}">
                            <i data-feather="check-circle" class="me-2"></i>
                            Validate Submission
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="text-muted">
                                You are about to validate the submission from <strong>{{ $submission->student ? $submission->student->first_name . ' ' . $submission->student->last_name : 'Unknown' }}</strong>
                                for <strong>{{ $submission->product->variation_title }}</strong>.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="approval_message{{ $submission->id }}" class="form-label">
                                <strong>Approval Message <span class="text-muted">(Optional)</span></strong>
                            </label>
                            <textarea name="approval_message" id="approval_message{{ $submission->id }}"
                                      class="form-control" rows="4" maxlength="1000"
                                      placeholder="Add a personalized congratulatory message for the student (optional). This will be sent via email along with the validation notification."></textarea>
                            <div class="form-text">Optional. A personal message to congratulate the student on their excellent work.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i data-feather="check" class="me-1"></i>
                            Validate & Notify Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!-- Rejection Modals -->
@foreach($studentStageCourses as $submission)
    @if($submission->is_valid == 0)
    <div class="modal fade" id="rejectModal{{ $submission->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $submission->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('student-stage-courses.reject', $submission) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel{{ $submission->id }}">
                            <i data-feather="x-circle" class="me-2"></i>
                            Reject Submission
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="text-muted">
                                You are about to reject the submission from <strong>{{ $submission->student ? $submission->student->first_name . ' ' . $submission->student->last_name : 'Unknown' }}</strong>
                                for <strong>{{ $submission->product->variation_title }}</strong>.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason{{ $submission->id }}" class="form-label">
                                <strong>Rejection Reason <span class="text-danger">*</span></strong>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason{{ $submission->id }}"
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
@endforeach
@endsection
