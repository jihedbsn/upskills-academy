@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Pays</h1>
        <div class="page-actions">
            <a href="{{ route('countries.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                Nouveau Pays
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="globe" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Liste des Pays</h4>
                            <p class="text-white-50 mb-0">Gérez tous les pays disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($countries->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pays</th>
                                        <th>Date de création</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $country)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ $country->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="flag" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark">{{ $country->titre }}</div>
                                                        <small class="text-muted">Pays</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $country->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="actions-column">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('countries.show', $country) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('countries.edit', $country) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <form action="{{ route('countries.destroy', $country) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pays ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $countries->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="globe" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">Aucun pays trouvé</h5>
                            <p class="text-muted">Commencez par créer votre premier pays</p>
                            <a href="{{ route('countries.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Créer un pays
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
