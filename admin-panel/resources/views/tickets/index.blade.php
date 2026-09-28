@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Gestion des Tickets de Support</h4>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Statut</label>
                        <select class="form-select" id="status_filter">
                            <option value="">Tous</option>
                            <option value="0">Ouverts</option>
                            <option value="1">Résolus</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="student_filter" class="form-label">Étudiant</label>
                        <input type="text" class="form-control" id="student_filter" placeholder="Nom ou email">
                    </div>
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary d-block" onclick="applyFilters()">
                            <i data-feather="filter"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Tableau des tickets -->
                <div class="table-responsive">
                    <table class="table table-striped" id="tickets-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>Sujet</th>
                                <th>Statut</th>
                                <th>Réponses</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr>
                                <td>#TKT-{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content">{{ substr($ticket->student->first_name, 0, 1) }}{{ substr($ticket->student->last_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $ticket->student->first_name }} {{ $ticket->student->last_name }}</h6>
                                            <small class="text-muted">{{ $ticket->student->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $ticket->sujet }}">
                                        {{ Str::limit($ticket->sujet, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @if($ticket->ticket_iscomplet)
                                        <span class="badge bg-success">Résolu</span>
                                    @else
                                        <span class="badge bg-warning">Ouvert</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $ticket->responses->count() }}</span>
                                </td>
                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i data-feather="more-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.support-tickets.show', $ticket->id) }}">
                                                <i data-feather="eye" class="me-2"></i>Voir
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($ticket->ticket_iscomplet)
                                                <li><a class="dropdown-item text-warning" href="#" onclick="toggleStatus({{ $ticket->id }})">
                                                    <i data-feather="rotate-ccw" class="me-2"></i>Réouvrir
                                                </a></li>
                                            @else
                                                <li><a class="dropdown-item text-success" href="#" onclick="toggleStatus({{ $ticket->id }})">
                                                    <i data-feather="check-circle" class="me-2"></i>Marquer comme résolu
                                                </a></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTicket({{ $ticket->id }})">
                                                <i data-feather="trash-2" class="me-2"></i>Supprimer
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i data-feather="inbox" class="mb-2" style="width: 48px; height: 48px;"></i>
                                        <p>Aucun ticket trouvé</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tickets->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $tickets->links() }}
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
                        <small>Total Tickets</small>
                    </div>
                    <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['open'] }}</h4>
                        <small>Ouverts</small>
                    </div>
                    <i data-feather="alert-circle" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['resolved'] }}</h4>
                        <small>Résolus</small>
                    </div>
                    <i data-feather="check-circle" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['this_month'] }}</h4>
                        <small>Ce Mois</small>
                    </div>
                    <i data-feather="calendar" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function applyFilters() {
    const status = document.getElementById('status_filter').value;
    const student = document.getElementById('student_filter').value;
    const date = document.getElementById('date_filter').value;
    
    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (student) url.searchParams.set('student', student);
    else url.searchParams.delete('student');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    
    window.location.href = url.toString();
}

function toggleStatus(ticketId) {
    if (!confirm('Êtes-vous sûr de vouloir modifier le statut de ce ticket ?')) {
        return;
    }
    
    fetch(`/admin/support-tickets/${ticketId}/toggle-status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    });
}

function deleteTicket(ticketId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?')) {
        return;
    }
    
    fetch(`/admin/support-tickets/${ticketId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
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
</script>
@endpush
