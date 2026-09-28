@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Modifier la Soumission de Stage</h1>
        <div class="page-actions">
            <a href="{{ route('student-stage-courses.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('student-stage-courses.show', $studentStageCourse) }}" class="btn btn-info">
                <i data-feather="eye" class="me-2"></i>
                Voir les détails
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="edit" class="me-2"></i>
                        Modifier les informations
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student-stage-courses.update', $studentStageCourse) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Étudiant</label>
                                    <input type="text" class="form-control" value="{{ $studentStageCourse->student ? $studentStageCourse->student->first_name . ' ' . $studentStageCourse->student->last_name . ' (' . $studentStageCourse->student->email . ')' : 'Student not found' }}" readonly>
                                    <input type="hidden" name="student_id" value="{{ $studentStageCourse->student_id }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produit</label>
                                    <input type="text" class="form-control" value="{{ $studentStageCourse->product?->variation_title ?? 'N/A' }}" readonly>
                                    <input type="hidden" name="product_id" value="{{ $studentStageCourse->product_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Décrivez le contenu de la soumission de stage..." required>{{ old('description', $studentStageCourse->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fichiers existants -->
                        @if($studentStageCourse->file1 || $studentStageCourse->file2)
                            <div class="mb-3">
                                <label class="form-label">Fichiers actuels</label>
                                <div class="row">
                                    @if($studentStageCourse->file1)
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @if($studentStageCourse->file1_type === 'image')
                                                                <i data-feather="image" class="text-primary"></i>
                                                            @elseif($studentStageCourse->file1_type === 'pdf')
                                                                <i data-feather="file-text" class="text-danger"></i>
                                                            @else
                                                                <i data-feather="file" class="text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
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
                                                                <i data-feather="image" class="text-primary"></i>
                                                            @elseif($studentStageCourse->file2_type === 'pdf')
                                                                <i data-feather="file-text" class="text-danger"></i>
                                                            @else
                                                                <i data-feather="file" class="text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
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
                                <div class="form-text">Sélectionner de nouveaux fichiers pour remplacer les fichiers actuels.</div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file1" class="form-label">Nouveau fichier 1</label>
                                    <input type="file" name="file1" id="file1"
                                           class="form-control @error('file1') is-invalid @enderror"
                                           accept=".pdf,.jpg,.jpeg,.png,.gif">
                                    <div class="form-text">Formats acceptés: PDF, JPG, JPEG, PNG, GIF (max 10MB)</div>
                                    @error('file1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file2" class="form-label">Nouveau fichier 2</label>
                                    <input type="file" name="file2" id="file2"
                                           class="form-control @error('file2') is-invalid @enderror"
                                           accept=".pdf,.jpg,.jpeg,.png,.gif">
                                    <div class="form-text">Formats acceptés: PDF, JPG, JPEG, PNG, GIF (max 10MB)</div>
                                    @error('file2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_valid" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="is_valid" id="is_valid" class="form-select @error('is_valid') is-invalid @enderror" required>
                                <option value="0" {{ old('is_valid', $studentStageCourse->is_valid) == '0' ? 'selected' : '' }}>En attente</option>
                                <option value="1" {{ old('is_valid', $studentStageCourse->is_valid) == '1' ? 'selected' : '' }}>Validé</option>
                                <option value="-1" {{ old('is_valid', $studentStageCourse->is_valid) == '-1' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                            @error('is_valid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Notes de l'administrateur</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3"
                                      class="form-control @error('admin_notes') is-invalid @enderror"
                                      placeholder="Notes internes pour l'administration...">{{ old('admin_notes', $studentStageCourse->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="approval_message" class="form-label">
                                Message d'approbation (optionnel)
                                <small class="text-muted">- Sera envoyé à l'étudiant par email si le stage est validé</small>
                            </label>
                            <textarea name="approval_message" id="approval_message" rows="3"
                                      class="form-control @error('approval_message') is-invalid @enderror"
                                      placeholder="Ajoutez un message personnalisé pour féliciter l'étudiant...">{{ old('approval_message', $studentStageCourse->approval_message) }}</textarea>
                            @error('approval_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('student-stage-courses.show', $studentStageCourse) }}" class="btn btn-secondary">
                                <i data-feather="x" class="me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
