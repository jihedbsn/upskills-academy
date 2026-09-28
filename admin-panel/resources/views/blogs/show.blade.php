@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="eye" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Détails de l'article</h4>
                                <p class="text-white-50 mb-0">Aperçu complet de l'article "{{ $blog->titre_en }}"</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <a href="{{ route('blogs.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Image de l'article -->
                    @if($blog->image)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="text-center">
                                    <img src="{{ asset('uploads/blogs/' . $blog->image) }}" alt="Image de l'article" class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="info" class="me-2"></i>
                                        Informations générales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">ID de l'article</label>
                                            <p class="mb-0">{{ $blog->id }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Slug</label>
                                            <p class="mb-0">{{ $blog->slug }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Statut</label>
                                            <span class="badge {{ $blog->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $blog->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Vues</label>
                                            <p class="mb-0">{{ $blog->views_count }} vues</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Date de publication</label>
                                            <p class="mb-0">
                                                @if($blog->published_date)
                                                    {{ $blog->published_date->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Non publié</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Auteur</label>
                                            <p class="mb-0">
                                                @if($blog->author_ar || $blog->author_en)
                                                    {{ $blog->author_ar ?: $blog->author_en }}
                                                @else
                                                    <span class="text-muted">Non spécifié</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu Arabe -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Contenu Arabe
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Titre Arabe</label>
                                            <p class="mb-0">{{ $blog->titre_ar }}</p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Meta Title Arabe</label>
                                            <p class="mb-0">{{ $blog->meta_title_ar }}</p>
                                        </div>
                                        @if($blog->description_short_ar)
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Description Courte Arabe</label>
                                                <p class="mb-0">{{ $blog->description_short_ar }}</p>
                                            </div>
                                        @endif
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Description Arabe</label>
                                            <div style="direction: rtl; text-align: right;">
                                                {!! $blog->description_ar !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu Anglais -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Contenu Anglais
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Titre Anglais</label>
                                            <p class="mb-0">{{ $blog->titre_en }}</p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Meta Title Anglais</label>
                                            <p class="mb-0">{{ $blog->meta_title_en }}</p>
                                        </div>
                                        @if($blog->description_short_en)
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Description Courte Anglais</label>
                                                <p class="mb-0">{{ $blog->description_short_en }}</p>
                                            </div>
                                        @endif
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Description Anglais</label>
                                            <div style="direction: ltr; text-align: left;">
                                                {!! $blog->description_en !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations temporelles -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="clock" class="me-2"></i>
                                        Informations temporelles
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Créé le</label>
                                            <p class="mb-0">{{ $blog->created_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Modifié le</label>
                                            <p class="mb-0">{{ $blog->updated_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('blogs.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Retour à la liste
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-warning">
                                        <i data-feather="edit" class="me-2"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-label {
    color: #6e6b7b;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
