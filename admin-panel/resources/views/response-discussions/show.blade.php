@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Détails de la réponse</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informations de la discussion</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID Discussion:</strong></td>
                                <td>{{ $responseDiscussion->discussion->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Étudiant:</strong></td>
                                <td>{{ $responseDiscussion->discussion->student ? $responseDiscussion->discussion->student->full_name : 'Student Deleted' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cours:</strong></td>
                                <td>{{ $responseDiscussion->discussion->product ? $responseDiscussion->discussion->product->titre : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Commentaire:</strong></td>
                                <td>{{ $responseDiscussion->discussion->commentaire }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut Discussion:</strong></td>
                                <td>
                                    @if($responseDiscussion->discussion->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Informations de la réponse</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID Réponse:</strong></td>
                                <td>{{ $responseDiscussion->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Admin:</strong></td>
                                <td>{{ $responseDiscussion->admin->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email Admin:</strong></td>
                                <td>{{ $responseDiscussion->admin->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut Réponse:</strong></td>
                                <td>
                                    @if($responseDiscussion->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Date de création:</strong></td>
                                <td>{{ $responseDiscussion->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Réponse</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $responseDiscussion->reponse }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.response-discussions.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Retour à la liste
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.response-discussions.edit', $responseDiscussion) }}" class="btn btn-warning">
                            <i data-feather="edit"></i> Modifier
                        </a>
                        @if(!$responseDiscussion->is_approved)
                            <button class="btn btn-success" onclick="approveResponse({{ $responseDiscussion->id }})">
                                <i data-feather="check"></i> Approuver
                            </button>
                        @else
                            <button class="btn btn-danger" onclick="disapproveResponse({{ $responseDiscussion->id }})">
                                <i data-feather="x"></i> Désapprouver
                            </button>
                        @endif
                        <form action="{{ route('admin.response-discussions.destroy', $responseDiscussion) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i data-feather="trash-2"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
