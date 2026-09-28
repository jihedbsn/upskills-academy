@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="video" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Gestion des Vidéos</h4>
                            <p class="text-white-50 mb-0">Gérez les vidéos de la ressource : {{ $resource->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Informations sur la ressource -->
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i data-feather="info" class="me-3" style="width: 20px; height: 20px;"></i>
                            <div>
                                <strong>Ressource :</strong> {{ $resource->name }} ({{ $resource->type }})
                                <br>
                                <strong>Fichier :</strong> {{ $resource->file }}
                            </div>
                        </div>
                    </div>

                    <!-- Liste des vidéos existantes -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i data-feather="video" class="me-2" style="width: 18px; height: 18px;"></i>
                            Fichiers vidéo actuels
                        </h5>
                        
                        @if($resource->video_files && count($resource->video_files) > 0)
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
                                                       class="btn btn-sm btn-outline-success me-1">
                                                        <i data-feather="download" class="me-1"></i>
                                                        Télécharger
                                                    </a>
                                                    <form action="{{ route('resources.remove-video-file', $resource) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="video_title" value="{{ $video['title'] }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                    <i data-feather="video" class="text-muted" style="width: 32px; height: 32px;"></i>
                                </div>
                                <h6 class="text-muted">Aucun fichier vidéo associé</h6>
                                <p class="text-muted">Ajoutez des vidéos avec fichiers à cette ressource</p>
                            </div>
                        @endif
                    </div>

                    <!-- Formulaire pour ajouter une nouvelle vidéo -->
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i data-feather="plus" class="me-2" style="width: 18px; height: 18px;"></i>
                                Ajouter une nouvelle vidéo avec fichier
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('resources.add-video-file', $resource) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="video_title" class="form-label">Titre de la vidéo</label>
                                            <input type="text" class="form-control @error('video_title') is-invalid @enderror" 
                                                   id="video_title" name="video_title" 
                                                   placeholder="Ex: Introduction, Chapitre 1, Conclusion, etc." required>
                                            @error('video_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="video_file" class="form-label">Fichier vidéo</label>
                                            <input type="file" class="form-control @error('video_file') is-invalid @enderror" 
                                                   id="video_file" name="video_file" 
                                                   accept="video/*,audio/*" required>
                                            @error('video_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i data-feather="plus" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Ajouter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    Formats acceptés: MP4, AVI, MOV, MP3. Taille maximale: 10MB
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('resources.show', $resource) }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Retour à la ressource
                        </a>
                        <a href="{{ route('resources.edit', $resource) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-2"></i>
                            Modifier la ressource
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
