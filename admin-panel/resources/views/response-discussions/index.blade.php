@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Gestion des Réponses aux Discussions</h4>
                <div class="card-actions">
                    <a href="{{ route('admin.response-discussions.create') }}" class="btn btn-primary">
                        <i data-feather="plus"></i> Nouvelle Réponse
                    </a>
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
                        <label for="discussion_filter" class="form-label">Discussion</label>
                        <select class="form-select" id="discussion_filter">
                            <option value="">Toutes</option>
                            @foreach($discussions as $discussion)
                                <option value="{{ $discussion->id }}">{{ $discussion->product ? Str::limit($discussion->product->titre, 30) : 'N/A' }} - {{ $discussion->student ? Str::limit($discussion->student->full_name, 20) : 'Student Deleted' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="admin_filter" class="form-label">Admin</label>
                        <input type="text" class="form-control" id="admin_filter" placeholder="Nom ou email">
                    </div>
                    <div class="col-md-2">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary d-block" onclick="applyFilters()">
                            <i data-feather="filter"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Tableau des réponses -->
                <div class="table-responsive">
                    <table class="table table-striped" id="responses-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Discussion</th>
                                <th>Étudiant</th>
                                <th>Cours</th>
                                <th>Admin</th>
                                <th>Réponse</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($responses as $response)
                            <tr>
                                <td>{{ $response->id }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $response->discussion->commentaire }}">
                                        {{ Str::limit($response->discussion->commentaire, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @if($response->discussion && $response->discussion->student)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content">{{ $response->discussion->student->initials }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $response->discussion->student->full_name }}</h6>
                                            <small class="text-muted">{{ $response->discussion->student->email }}</small>
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
                                    <span class="badge bg-primary">{{ $response->discussion && $response->discussion->product ? $response->discussion->product->titre : 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($response->admin)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content">{{ $response->admin->initials }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $response->admin->full_name }}</h6>
                                            <small class="text-muted">{{ $response->admin->email }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content bg-secondary">?</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Admin Deleted</h6>
                                            <small class="text-muted">N/A</small>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $response->reponse }}">
                                        {{ Str::limit($response->reponse, 100) }}
                                    </div>
                                </td>
                                <td>
                                    @if($response->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted">{{ $response->created_at->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $response->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.response-discussions.show', $response) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <a href="{{ route('admin.response-discussions.edit', $response) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        @if(!$response->is_approved)
                                            <button class="btn btn-sm btn-outline-success" title="Approuver" onclick="approveResponse({{ $response->id }})">
                                                <i data-feather="check" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-danger" title="Désapprouver" onclick="disapproveResponse({{ $response->id }})">
                                                <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('admin.response-discussions.destroy', $response) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i data-feather="message-circle" style="width: 48px; height: 48px;" class="mb-3"></i>
                                        <h5>Aucune réponse trouvée</h5>
                                        <p>Commencez par créer une nouvelle réponse</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $responses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-circle me-3">
                        <i data-feather="message-circle" class="text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <p class="mb-0">Total Réponses</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-circle me-3">
                        <i data-feather="check-circle" class="text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                        <p class="mb-0">Approuvées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-circle me-3">
                        <i data-feather="clock" class="text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        <p class="mb-0">En attente</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const status = document.getElementById('status_filter').value;
    const discussion = document.getElementById('discussion_filter').value;
    const admin = document.getElementById('admin_filter').value;
    const date = document.getElementById('date_filter').value;

    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (discussion) url.searchParams.set('discussion', discussion);
    else url.searchParams.delete('discussion');
    
    if (admin) url.searchParams.set('admin', admin);
    else url.searchParams.delete('admin');
    
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');

    window.location.href = url.toString();
}

function approveResponse(id) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette réponse ?')) {
        fetch(`/admin/response-discussions/${id}/approve`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
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
}

function disapproveResponse(id) {
    if (confirm('Êtes-vous sûr de vouloir désapprouver cette réponse ?')) {
        fetch(`/admin/response-discussions/${id}/disapprove`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la désapprobation');
            }
        });
    }
}
</script>
@endsection
