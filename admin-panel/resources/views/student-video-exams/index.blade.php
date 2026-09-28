@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Examens Vidéo</h1>
        <div class="page-actions">
            <a href="{{ route('student-video-exams.by-product') }}" class="btn btn-info">
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
                    <form method="GET" action="{{ route('student-video-exams.index') }}" class="row">
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Étudiant, produit, lien, description...">
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

    <!-- Tableau des examens vidéo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="video" class="me-2"></i>
                        Liste des Examens Vidéo
                    </h4>
                </div>
                <div class="card-body">
                    @if($studentVideoExams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Étudiant</th>
                                        <th>Produit</th>
                                        <th>Lien vidéo</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Date de soumission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentVideoExams as $videoExam)
                                        <tr>
                                            <td>{{ $videoExam->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $videoExam->student ? $videoExam->student->first_name . ' ' . $videoExam->student->last_name : 'Student not found' }}</h6>
                                                        <small class="text-muted">{{ $videoExam->student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $videoExam->product->variation_title }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ $videoExam->lien }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                        <i data-feather="external-link"></i>
                                                    </a>
                                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $videoExam->lien }}">
                                                        {{ $videoExam->lien }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $videoExam->video_description }}">
                                                    {{ $videoExam->video_description }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $videoExam->status_class }}">
                                                    {{ $videoExam->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $videoExam->submitted_at ? $videoExam->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('student-video-exams.show', $videoExam) }}"
                                                       class="btn btn-sm btn-outline-info" title="Voir">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                    <a href="{{ route('student-video-exams.edit', $videoExam) }}"
                                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                    @if($videoExam->is_valid == 0)
                                                        <form method="POST" action="{{ route('student-video-exams.validate', $videoExam) }}"
                                                              class="d-inline" onsubmit="return confirm('Valider cet examen vidéo ?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Valider">
                                                                <i data-feather="check"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Rejeter"
                                                                data-bs-toggle="modal" data-bs-target="#rejectVideoModal{{ $videoExam->id }}">
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
                            {{ $studentVideoExams->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="video-off" class="feather-48 text-muted"></i>
                            <h4 class="mt-3 text-muted">Aucun examen vidéo trouvé</h4>
                            <p class="text-muted">Il n'y a pas d'examens vidéo correspondant à vos critères.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modals for Video Exams -->
@foreach($studentVideoExams as $videoExam)
    @if($videoExam->is_valid == 0)
    <div class="modal fade" id="rejectVideoModal{{ $videoExam->id }}" tabindex="-1" aria-labelledby="rejectVideoModalLabel{{ $videoExam->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('student-video-exams.reject', $videoExam) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectVideoModalLabel{{ $videoExam->id }}">
                            <i data-feather="x-circle" class="me-2"></i>
                            Reject Video Exam
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="text-muted">
                                You are about to reject the video exam from <strong>{{ $videoExam->student ? $videoExam->student->first_name . ' ' . $videoExam->student->last_name : 'Unknown' }}</strong>
                                for <strong>{{ $videoExam->product->variation_title }}</strong>.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason{{ $videoExam->id }}" class="form-label">
                                <strong>Rejection Reason <span class="text-danger">*</span></strong>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason{{ $videoExam->id }}"
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
