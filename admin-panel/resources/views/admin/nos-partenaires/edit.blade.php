@extends('layouts.app')

@section('title', 'Modifier le Partenaire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Partenaire: {{ $nosPartenaires->nom }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('nos-partenaires.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('nos-partenaires.update', $nosPartenaires) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nom">Nom du Partenaire <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $nosPartenaires->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="url">URL du Site Web</label>
                                    <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                           id="url" name="url" value="{{ old('url', $nosPartenaires->url) }}" 
                                           placeholder="https://example.com">
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="order">Ordre d'Affichage</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           id="order" name="order" value="{{ old('order', $nosPartenaires->order) }}" 
                                           min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Plus le nombre est petit, plus le partenaire apparaîtra en premier.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" 
                                               name="is_active" value="1" 
                                               {{ old('is_active', $nosPartenaires->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Partenaire actif
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Seuls les partenaires actifs seront affichés sur le site.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="logo">Logo du Partenaire</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                               id="logo" name="logo" accept="image/*">
                                        <label class="custom-file-label" for="logo">Choisir un nouveau fichier</label>
                                    </div>
                                    @error('logo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Formats acceptés: JPG, PNG, GIF, SVG. Taille max: 2MB.
                                        Laisser vide pour conserver le logo actuel.
                                    </small>
                                </div>

                                @if($nosPartenaires->logo)
                                    <div class="mt-3">
                                        <label>Logo actuel:</label>
                                        <div class="border p-2 text-center">
                                            <img src="{{ $nosPartenaires->logo_url }}" alt="{{ $nosPartenaires->nom }}" 
                                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>
                                @endif

                                <div id="logo-preview" class="mt-3" style="display: none;">
                                    <label>Nouveau logo:</label>
                                    <div class="border p-2 text-center">
                                        <img id="preview-img" src="" alt="Aperçu" 
                                             class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                    <a href="{{ route('nos-partenaires.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </div>
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
$(document).ready(function() {
    // Aperçu du nouveau logo
    $('#logo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#logo-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#logo-preview').hide();
        }
    });

    // Mise à jour du label du fichier
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>
@endpush
