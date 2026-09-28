@extends('layouts.app')

@section('title', 'Détails du Partenaire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails du Partenaire: {{ $nosPartenaires->nom }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('nos-partenaires.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        <a href="{{ route('nos-partenaires.edit', $nosPartenaires) }}" class="btn btn-warning ml-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Nom:</th>
                                    <td><strong>{{ $nosPartenaires->nom }}</strong></td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td>
                                        @if($nosPartenaires->url)
                                            <a href="{{ $nosPartenaires->url }}" target="_blank" class="text-primary">
                                                {{ $nosPartenaires->url }}
                                                <i class="fas fa-external-link-alt ml-1"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">Aucune URL définie</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ordre d'affichage:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $nosPartenaires->order }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut:</th>
                                    <td>
                                        @if($nosPartenaires->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de création:</th>
                                    <td>{{ $nosPartenaires->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière modification:</th>
                                    <td>{{ $nosPartenaires->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Logo du Partenaire</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($nosPartenaires->logo)
                                        <img src="{{ $nosPartenaires->logo_url }}" alt="{{ $nosPartenaires->nom }}" 
                                             class="img-fluid rounded shadow" style="max-width: 100%; max-height: 200px;">
                                        <p class="mt-2 text-muted">
                                            <small>Cliquez sur l'image pour l'agrandir</small>
                                        </p>
                                    @else
                                        <div class="bg-light p-5 rounded">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="mt-2 text-muted">Aucun logo défini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('nos-partenaires.edit', $nosPartenaires) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <form action="{{ route('nos-partenaires.destroy', $nosPartenaires) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Agrandir l'image au clic
    $('img').on('click', function() {
        const src = $(this).attr('src');
        if (src) {
            const modal = `
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Logo du Partenaire</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="${src}" class="img-fluid" alt="Logo agrandi">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modal);
            $('#imageModal').modal('show');
            
            $('#imageModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    });
});
</script>
@endpush
