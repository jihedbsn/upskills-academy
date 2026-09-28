@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Ticket Information Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Ticket #TKT-{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</h4>
                    <div>
                        @if($ticket->ticket_iscomplet)
                            <span class="badge bg-success me-2">Résolu</span>
                            <button class="btn btn-sm btn-warning" onclick="toggleStatus({{ $ticket->id }})">
                                <i data-feather="rotate-ccw"></i> Réouvrir
                            </button>
                        @else
                            <span class="badge bg-warning me-2">Ouvert</span>
                            <button class="btn btn-sm btn-success" onclick="toggleStatus({{ $ticket->id }})">
                                <i data-feather="check-circle"></i> Marquer comme résolu
                            </button>
                        @endif
                        <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-sm btn-secondary ms-2">
                            <i data-feather="arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations du Ticket</h6>
                        <ul class="list-unstyled">
                            <li><strong>Sujet:</strong> {{ $ticket->sujet }}</li>
                            <li><strong>Créé le:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Mis à jour le:</strong> {{ $ticket->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations de l'Étudiant</h6>
                        <ul class="list-unstyled">
                            <li><strong>Nom:</strong> {{ $ticket->student->first_name }} {{ $ticket->student->last_name }}</li>
                            <li><strong>Email:</strong> {{ $ticket->student->email }}</li>
                            @if($ticket->student->phone)
                            <li><strong>Téléphone:</strong> {{ $ticket->student->phone }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Thread -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Conversation</h5>
            </div>
            <div class="card-body">
                <div class="conversation-thread" style="max-height: 500px; overflow-y: auto;">
                    @forelse($ticket->responses as $response)
                        <div class="message-item mb-3 p-3 border rounded {{ $response->isAdmin ? 'bg-light' : 'bg-primary bg-opacity-10' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        @if($response->isAdmin)
                                            <span class="avatar-content bg-danger">A</span>
                                        @else
                                            <span class="avatar-content bg-primary">{{ substr($ticket->student->first_name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <strong class="{{ $response->isAdmin ? 'text-danger' : 'text-primary' }}">
                                        {{ $response->isAdmin ? 'Admin' : $ticket->student->first_name . ' ' . $ticket->student->last_name }}
                                    </strong>
                                </div>
                                <small class="text-muted">{{ $response->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $response->message }}</p>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i data-feather="message-circle" style="width: 48px; height: 48px;"></i>
                            <p>Aucune réponse pour le moment</p>
                        </div>
                    @endforelse
                </div>

                <!-- Response Form -->
                <div class="mt-4">
                    <h6>Ajouter une Réponse</h6>
                    <form action="{{ route('admin.support-tickets.respond', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4" placeholder="Tapez votre réponse ici..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="send"></i> Envoyer la Réponse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

// Auto-scroll to bottom of conversation
document.addEventListener('DOMContentLoaded', function() {
    const conversationThread = document.querySelector('.conversation-thread');
    if (conversationThread) {
        conversationThread.scrollTop = conversationThread.scrollHeight;
    }
});
</script>
@endpush
