@extends('layouts.app')

@push('styles')
<style>
    #bulk-approve-btn,
    #bulk-delete-btn {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Gestion des Discussions</h4>
                <div class="card-actions">
                    <button class="btn btn-success" onclick="bulkApprove()" id="bulk-approve-btn">
                        <i data-feather="check"></i> Approuver Sélection
                    </button>
                    <button class="btn btn-danger ms-2" onclick="bulkDelete()" id="bulk-delete-btn">
                        <i data-feather="trash-2"></i> Supprimer Sélection
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="status_filter" class="form-label">Statut</label>
                        <select class="form-select" id="status_filter">
                            <option value="">Tous</option>
                            <option value="1">Approuvé</option>
                            <option value="0">En attente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="product_filter" class="form-label">Cours</label>
                        <select class="form-select" id="product_filter">
                            <option value="">Tous</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="student_filter" class="form-label">Étudiant</label>
                        <input type="text" class="form-control" id="student_filter" placeholder="Nom ou email">
                    </div>
                    <div class="col-md-2">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-2">
                        <label for="has_responses_filter" class="form-label">Réponses</label>
                        <select class="form-select" id="has_responses_filter">
                            <option value="">Tous</option>
                            <option value="1">Avec réponses</option>
                            <option value="0">Sans réponses</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary d-block" onclick="applyFilters()">
                            <i data-feather="filter"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Tableau des discussions -->
                <div class="table-responsive">
                    <table class="table table-striped" id="discussions-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                </th>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>Cours</th>
                                <th>Commentaire</th>
                                <th>Statut</th>
                                <th>Réponses</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discussions as $discussion)
                            <tr>
                                <td>
                                    <input type="checkbox" class="discussion-checkbox" value="{{ $discussion->id }}">
                                </td>
                                <td>{{ $discussion->id }}</td>
                                <td>
                                    @if($discussion->student)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content">{{ $discussion->student->initials }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $discussion->student->full_name }}</h6>
                                            <small class="text-muted">{{ $discussion->student->email }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content bg-secondary">?</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Student Deleted</h6>
                                            <small class="text-muted">N/A</small>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $discussion->product ? $discussion->product->titre : 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $discussion->commentaire }}">
                                        {{ Str::limit($discussion->commentaire, 100) }}
                                    </div>
                                </td>
                                <td>
                                    @if($discussion->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $discussion->responses->count() }}</span>
                                </td>
                                <td>{{ $discussion->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.discussions.show', $discussion) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <a href="{{ route('admin.discussions.edit', $discussion) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i data-feather="edit"></i>
                                        </a>
                                        @if($discussion->is_approved)
                                            <form action="{{ route('admin.discussions.disapprove', $discussion) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Désapprouver" onclick="return confirm('Désapprouver cette discussion ?')">
                                                    <i data-feather="x-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.discussions.approve', $discussion) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approuver" onclick="return confirm('Approuver cette discussion ?')">
                                                    <i data-feather="check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button class="btn btn-sm btn-outline-info" onclick="addResponse({{ $discussion->id }})" title="Répondre">
                                            <i data-feather="message-circle"></i>
                                        </button>
                                        <form action="{{ route('admin.discussions.destroy', $discussion) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Supprimer cette discussion ?')">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i data-feather="message-circle" class="mb-2" style="width: 48px; height: 48px;"></i>
                                        <p>Aucune discussion trouvée</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($discussions->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $discussions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mt-3">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <small>Total Discussions</small>
                    </div>
                    <i data-feather="message-circle" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                        <small>Approuvées</small>
                    </div>
                    <i data-feather="check-circle" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        <small>En attente</small>
                    </div>
                    <i data-feather="clock" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_responses'] }}</h4>
                        <small>Total Réponses</small>
                    </div>
                    <i data-feather="message-square" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter une réponse -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une réponse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="responseForm">
                <div class="modal-body">
                    <input type="hidden" id="discussion_id" name="discussion_id">
                    <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                    <div class="mb-3">
                        <label for="response_text" class="form-label">Réponse</label>
                        <textarea class="form-control" id="response_text" name="reponse" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function applyFilters() {
    const status = document.getElementById('status_filter').value;
    const product = document.getElementById('product_filter').value;
    const student = document.getElementById('student_filter').value;
    const date = document.getElementById('date_filter').value;
    const hasResponses = document.getElementById('has_responses_filter').value;

    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (product) url.searchParams.set('product', product);
    if (student) url.searchParams.set('student', student);
    if (date) url.searchParams.set('date', date);
    if (hasResponses) url.searchParams.set('has_responses', hasResponses);

    window.location.href = url.toString();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.discussion-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    // Show/hide bulk action buttons
    updateBulkActionButtons();
}

function updateBulkActionButtons() {
    const selectedCount = document.querySelectorAll('.discussion-checkbox:checked').length;
    const bulkApproveBtn = document.getElementById('bulk-approve-btn');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    if (selectedCount > 0) {
        bulkApproveBtn.style.display = 'inline-block';
        bulkDeleteBtn.style.display = 'inline-block';
        bulkApproveBtn.innerHTML = `<i data-feather="check"></i> Approuver (${selectedCount})`;
        bulkDeleteBtn.innerHTML = `<i data-feather="trash-2"></i> Supprimer (${selectedCount})`;
        feather.replace();
    } else {
        bulkApproveBtn.style.display = 'none';
        bulkDeleteBtn.style.display = 'none';
    }
}

// Add change listener to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.discussion-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });
    updateBulkActionButtons(); // Initial state
});

function bulkApprove() {
    const selectedIds = Array.from(document.querySelectorAll('.discussion-checkbox:checked'))
                           .map(checkbox => checkbox.value);

    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins une discussion');
        return;
    }

    if (!confirm(`Êtes-vous sûr de vouloir approuver ${selectedIds.length} discussion(s) ?`)) {
        return;
    }

    fetch('/admin/discussions/bulk-approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ discussion_ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'approbation');
        }
    });
}

function bulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.discussion-checkbox:checked'))
                           .map(checkbox => checkbox.value);

    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins une discussion');
        return;
    }

    if (!confirm(`⚠️ ATTENTION: Êtes-vous sûr de vouloir supprimer ${selectedIds.length} discussion(s) ?\n\nCette action est irréversible!`)) {
        return;
    }

    fetch('/admin/discussions/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ discussion_ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la suppression');
        }
    });
}

function addResponse(discussionId) {
    event.preventDefault();
    document.getElementById('discussion_id').value = discussionId;
    new bootstrap.Modal(document.getElementById('responseModal')).show();
}

document.getElementById('responseForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/admin/response-discussions', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('responseModal')).hide();
            location.reload();
        } else {
            alert('Erreur lors de l\'ajout de la réponse');
        }
    });
});
</script>
@endpush
