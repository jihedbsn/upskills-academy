@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="globe" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Détails du Pays</h4>
                                <p class="text-white-50 mb-0">Informations complètes sur le pays</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('countries.edit', $country) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('countries.destroy', $country) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pays ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i data-feather="trash-2" class="me-2"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="hash" class="me-2" style="width: 16px; height: 16px;"></i>
                                    ID
                                </label>
                                <p class="form-control-plaintext">{{ $country->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="flag" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Nom du pays
                                </label>
                                <p class="form-control-plaintext">{{ $country->titre }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Date de création
                                </label>
                                <p class="form-control-plaintext">{{ $country->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Dernière modification
                                </label>
                                <p class="form-control-plaintext">{{ $country->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('countries.edit', $country) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-2"></i>
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
