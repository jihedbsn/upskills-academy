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
                                    <i data-feather="file" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Détails de la Ressource</h4>
                                <p class="text-white-50 mb-0">Informations complètes sur la ressource</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('resources.download', $resource) }}" class="btn btn-success">
                                <i data-feather="download" class="me-2"></i>
                                Télécharger
                            </a>
                            <a href="{{ route('resources.edit', $resource) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')">
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
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="hash" class="me-2" style="width: 16px; height: 16px;"></i>
                                    ID
                                </label>
                                <p class="form-control-plaintext">{{ $resource->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Nom (Arabe)
                                </label>
                                <p class="form-control-plaintext">{{ $resource->name_ar ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Nom (Anglais)
                                </label>
                                <p class="form-control-plaintext">{{ $resource->name_en ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="tag" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Type
                                </label>
                                <p class="form-control-plaintext">
                                    @if($resource->type == 'video')
                                        <span class="badge bg-danger">
                                            <i data-feather="video" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Vidéo
                                        </span>
                                    @elseif($resource->type == 'book')
                                        <span class="badge bg-success">
                                            <i data-feather="book" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Livre
                                        </span>
                                    @elseif($resource->type == 'audio')
                                        <span class="badge bg-warning">
                                            <i data-feather="music" class="me-1" style="width: 14px; height: 14px;"></i>
                                            Audio
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $resource->type }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="file-text" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Nom du fichier
                                </label>
                                <p class="form-control-plaintext">{{ $resource->file }}</p>
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
                                <p class="form-control-plaintext">{{ $resource->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Dernière modification
                                </label>
                                <p class="form-control-plaintext">{{ $resource->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section pour les vidéos multiples (visible seulement si type = video) -->
                    @if($resource->type === 'video' && $resource->video_files && count($resource->video_files) > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i data-feather="video" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Vidéos avec fichiers
                                    </label>
                                    <div class="row">
                                        @foreach($resource->video_files as $index => $video)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border-primary">
                                                    <div class="card-body text-center">
                                                        <div class="mb-2">
                                                            <i data-feather="video" class="text-primary" style="width: 32px; height: 32px;"></i>
                                                        </div>
                                                        <h6 class="card-title mb-1">{{ $video['title'] }}</h6>
                                                        <small class="text-muted">Fichier: {{ $video['file'] }}</small>
                                                        <div class="mt-2">
                                                            <a href="{{ route('resources.download-video', ['resource' => $resource, 'title' => $video['title']]) }}" 
                                                               class="btn btn-sm btn-outline-success">
                                                                <i data-feather="download" class="me-1"></i>
                                                                Télécharger
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Dernière modification
                                </label>
                                <p class="form-control-plaintext">{{ $resource->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="download" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Actions
                                </label>
                                <div>
                                    <a href="{{ route('resources.download', $resource) }}" class="btn btn-sm btn-success">
                                        <i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i>
                                        Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Retour à la liste
                        </a>
                        <div>
                            @if($resource->type === 'video')
                                <a href="{{ route('resources.manage-videos', $resource) }}" class="btn btn-primary me-2">
                                    <i data-feather="video" class="me-2"></i>
                                    Gérer les vidéos
                                </a>
                            @endif
                            <a href="{{ route('resources.download', $resource) }}" class="btn btn-success me-2">
                                <i data-feather="download" class="me-2"></i>
                                Télécharger
                            </a>
                            <a href="{{ route('resources.edit', $resource) }}" class="btn btn-warning me-2">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')">
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
@endsection
