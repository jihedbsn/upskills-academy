@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Modifier la Discussion #{{ $discussion->id }}</h4>
                <a href="{{ route('admin.discussions.index') }}" class="btn btn-secondary btn-sm">
                    <i data-feather="arrow-left"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.discussions.update', $discussion->id) }}" method="POST" id="discussionEditForm">
                    @csrf
                    @method('PUT')

                    <!-- Hidden fields for student and product -->
                    <input type="hidden" name="student_id" value="{{ $discussion->student_id }}">
                    <input type="hidden" name="product_id" value="{{ $discussion->product_id }}">

                    <div class="row">
                        <!-- Student Display (Read-only) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Étudiant</label>
                                <div class="form-control-plaintext bg-light-3 rounded-8 px-15 py-10">
                                    @if($discussion->student)
                                        <strong>{{ $discussion->student->full_name }}</strong><br>
                                        <small class="text-muted">{{ $discussion->student->email }}</small>
                                    @else
                                        <span class="text-muted">Student Deleted</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Product Display (Read-only) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cours</label>
                                <div class="form-control-plaintext bg-light-3 rounded-8 px-15 py-10">
                                    @if($discussion->product)
                                        <span class="badge bg-primary">{{ $discussion->product->titre }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire *</label>
                                <textarea class="form-control @error('commentaire') is-invalid @enderror"
                                          id="commentaire"
                                          name="commentaire"
                                          rows="6"
                                          maxlength="1000"
                                          required>{{ old('commentaire', $discussion->commentaire) }}</textarea>
                                <div class="form-text">
                                    <span id="char-count">{{ strlen($discussion->commentaire) }}</span>/1000 caractères
                                </div>
                                @error('commentaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Approval Status -->
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_approved"
                                           name="is_approved"
                                           value="1"
                                           {{ old('is_approved', $discussion->is_approved) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_approved">
                                        <i data-feather="check-circle"></i> Approuver cette discussion
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.discussions.index') }}" class="btn btn-secondary">
                            <i data-feather="x"></i> Annuler
                        </a>
                        <div>
                            <a href="{{ route('admin.discussions.show', $discussion) }}" class="btn btn-info me-2">
                                <i data-feather="eye"></i> Voir
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Discussion Info Card -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="info"></i> Informations de la Discussion
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">ID:</td>
                                <td>{{ $discussion->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Statut actuel:</td>
                                <td>
                                    @if($discussion->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Créé le:</td>
                                <td>{{ $discussion->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Modifié le:</td>
                                <td>{{ $discussion->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nb de réponses:</td>
                                <td>
                                    <span class="badge bg-info">{{ $discussion->responses->count() }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($discussion->responses->count() > 0)
<!-- Responses Card -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="message-circle"></i> Réponses ({{ $discussion->responses->count() }})
                </h5>
            </div>
            <div class="card-body">
                @foreach($discussion->responses as $response)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $response->admin ? $response->admin->name : 'Admin supprimé' }}</strong>
                            <small class="text-muted ms-2">{{ $response->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <span class="badge {{ $response->is_approved ? 'bg-success' : 'bg-warning' }}">
                            {{ $response->is_approved ? 'Approuvé' : 'En attente' }}
                        </span>
                    </div>
                    <p class="mt-2 mb-0">{{ $response->reponse }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Character counter
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('commentaire');
        const charCount = document.getElementById('char-count');
        const form = document.getElementById('discussionEditForm');

        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Debug: Log form action before submit
        form.addEventListener('submit', function(e) {
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            console.log('Discussion ID:', document.querySelector('input[name="discussion_id"]').value);
        });

        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
