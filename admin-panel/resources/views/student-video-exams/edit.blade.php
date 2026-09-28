@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Modifier l'Examen Vidéo</h1>
        <div class="page-actions">
            <a href="{{ route('student-video-exams.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('student-video-exams.show', $studentVideoExam) }}" class="btn btn-info">
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
                    <form method="POST" action="{{ route('student-video-exams.update', $studentVideoExam) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Étudiant</label>
                                    <input type="text" class="form-control" value="{{ $studentVideoExam->student ? $studentVideoExam->student->first_name . ' ' . $studentVideoExam->student->last_name . ' (' . $studentVideoExam->student->email . ')' : 'Student not found' }}" readonly>
                                    <input type="hidden" name="student_id" value="{{ $studentVideoExam->student_id }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produit</label>
                                    <input type="text" class="form-control" value="{{ $studentVideoExam->product->variation_title }}" readonly>
                                    <input type="hidden" name="product_id" value="{{ $studentVideoExam->product_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="lien" class="form-label">Lien de la vidéo <span class="text-danger">*</span></label>
                            <input type="url" name="lien" id="lien"
                                   class="form-control @error('lien') is-invalid @enderror"
                                   value="{{ old('lien', $studentVideoExam->lien) }}"
                                   placeholder="https://www.youtube.com/watch?v=..." required>
                            <div class="form-text">URL complète de la vidéo (YouTube, Vimeo, etc.)</div>
                            @error('lien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="video_description" class="form-label">Description de la vidéo <span class="text-danger">*</span></label>
                            <textarea name="video_description" id="video_description" rows="4"
                                      class="form-control @error('video_description') is-invalid @enderror"
                                      placeholder="Décrivez le contenu de l'examen vidéo..." required>{{ old('video_description', $studentVideoExam->video_description) }}</textarea>
                            @error('video_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="is_valid" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="is_valid" id="is_valid" class="form-select @error('is_valid') is-invalid @enderror" required>
                                <option value="0" {{ old('is_valid', $studentVideoExam->is_valid) == '0' ? 'selected' : '' }}>En attente</option>
                                <option value="1" {{ old('is_valid', $studentVideoExam->is_valid) == '1' ? 'selected' : '' }}>Validé</option>
                                <option value="-1" {{ old('is_valid', $studentVideoExam->is_valid) == '-1' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                            @error('is_valid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="admin_notes_container" style="{{ old('is_valid', $studentVideoExam->is_valid) == '-1' ? '' : 'display: none;' }}">
                            <label for="admin_notes" class="form-label">
                                Admin Notes / Rejection Reason
                                <span class="text-danger" id="admin_notes_required" style="{{ old('is_valid', $studentVideoExam->is_valid) == '-1' ? '' : 'display: none;' }}">*</span>
                            </label>
                            <textarea name="admin_notes" id="admin_notes" rows="4"
                                      class="form-control @error('admin_notes') is-invalid @enderror"
                                      placeholder="Provide feedback or explain what needs to be fixed/adjusted for the submission to be accepted...">{{ old('admin_notes', $studentVideoExam->admin_notes) }}</textarea>
                            <div class="form-text">This message will be sent to the student if the video is rejected.</div>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                            document.getElementById('is_valid').addEventListener('change', function() {
                                const adminNotesContainer = document.getElementById('admin_notes_container');
                                const adminNotesField = document.getElementById('admin_notes');
                                const adminNotesRequired = document.getElementById('admin_notes_required');

                                if (this.value === '-1') {
                                    adminNotesContainer.style.display = 'block';
                                    adminNotesField.required = true;
                                    adminNotesRequired.style.display = 'inline';
                                } else {
                                    adminNotesContainer.style.display = 'none';
                                    adminNotesField.required = false;
                                    adminNotesRequired.style.display = 'none';
                                }
                            });
                        </script>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('student-video-exams.show', $studentVideoExam) }}" class="btn btn-secondary">
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
