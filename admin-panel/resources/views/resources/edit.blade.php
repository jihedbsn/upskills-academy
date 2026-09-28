@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Modifier la Ressource</h4>
                            <p class="text-white-50 mb-0">Modifiez les informations de la ressource</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Erreurs de validation</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('resources.update', $resource) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name_ar" class="form-label">
                                        <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Nom (Arabe) *
                                    </label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                           id="name_ar" name="name_ar" value="{{ old('name_ar', $resource->name_ar) }}"
                                           placeholder="اسم المورد" required>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name_en" class="form-label">
                                        <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Nom (Anglais) *
                                    </label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                           id="name_en" name="name_en" value="{{ old('name_en', $resource->name_en) }}"
                                           placeholder="Resource Name" required>
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="form-label">
                                <i data-feather="tag" class="me-2" style="width: 16px; height: 16px;"></i>
                                Type de ressource
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="video" {{ old('type', $resource->type) == 'video' ? 'selected' : '' }}>
                                    <i data-feather="video" class="me-2"></i>Vidéo
                                </option>
                                <option value="book" {{ old('type', $resource->type) == 'book' ? 'selected' : '' }}>
                                    <i data-feather="book" class="me-2"></i>Livre
                                </option>
                                <option value="audio" {{ old('type', $resource->type) == 'audio' ? 'selected' : '' }}>
                                    <i data-feather="music" class="me-2"></i>Audio
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Multilingual Files Section (hidden for video resources) -->
                        <div class="mb-4" id="main-file-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="file_ar" class="form-label">
                                        <i data-feather="upload" class="me-2" style="width: 16px; height: 16px;"></i>
                                        New File (Arabic) - Optional
                                    </label>
                                    <input type="file" class="form-control @error('file_ar') is-invalid @enderror"
                                           id="file_ar" name="file_ar">
                                    @if($resource->file_ar)
                                        <small class="text-success mt-1 d-block">
                                            <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                            Current: {{ $resource->file_ar }}
                                        </small>
                                    @else
                                        <small class="text-muted mt-1 d-block">No Arabic file uploaded</small>
                                    @endif
                                    @error('file_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="file_en" class="form-label">
                                        <i data-feather="upload" class="me-2" style="width: 16px; height: 16px;"></i>
                                        New File (English) - Optional
                                    </label>
                                    <input type="file" class="form-control @error('file_en') is-invalid @enderror"
                                           id="file_en" name="file_en">
                                    @if($resource->file_en)
                                        <small class="text-success mt-1 d-block">
                                            <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                            Current: {{ $resource->file_en }}
                                        </small>
                                    @else
                                        <small class="text-muted mt-1 d-block">No English file uploaded</small>
                                    @endif
                                    @error('file_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                Upload new files to replace existing ones. If only one language file is available, it will be used for both.
                            </div>
                        </div>

                        <!-- Current Files Info -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i data-feather="file-text" class="me-2" style="width: 16px; height: 16px;"></i>
                                Current Files
                            </label>
                            <div class="row">
                                @if($resource->file_ar)
                                <div class="col-md-6 mb-2">
                                    <div class="alert alert-info py-2">
                                        <strong>Arabic File:</strong> {{ $resource->file_ar }}
                                        <div class="mt-2">
                                            <a href="{{ asset('uploads/resources/' . $resource->file_ar) }}" target="_blank" class="btn btn-sm btn-success">
                                                <i data-feather="download" style="width: 14px; height: 14px;"></i> Download
                                            </a>
                                            <label class="btn btn-sm btn-outline-danger mb-0">
                                                <input type="checkbox" name="delete_file_ar" value="1" class="me-1"> Delete
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($resource->file_en)
                                <div class="col-md-6 mb-2">
                                    <div class="alert alert-info py-2">
                                        <strong>English File:</strong> {{ $resource->file_en }}
                                        <div class="mt-2">
                                            <a href="{{ asset('uploads/resources/' . $resource->file_en) }}" target="_blank" class="btn btn-sm btn-success">
                                                <i data-feather="download" style="width: 14px; height: 14px;"></i> Download
                                            </a>
                                            <label class="btn btn-sm btn-outline-danger mb-0">
                                                <input type="checkbox" name="delete_file_en" value="1" class="me-1"> Delete
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(!$resource->file_ar && !$resource->file_en && $resource->file)
                                <div class="col-12">
                                    <div class="alert alert-warning py-2">
                                        <strong>Legacy File:</strong> {{ $resource->file }}
                                        <small class="d-block text-muted">This file will be shown for all languages until you upload language-specific files.</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Section pour les vidéos multiples (visible seulement si type = video) -->
                        <div class="mb-4" id="videos-section" style="display: none;">
                            <label class="form-label">
                                <i data-feather="video" class="me-2" style="width: 16px; height: 16px;"></i>
                                Vidéos avec fichiers
                            </label>

                            <!-- Multilingual Video Files -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="alert alert-light border">
                                        <label class="form-label small fw-bold text-primary">
                                            <i data-feather="globe" style="width: 14px; height: 14px;"></i>
                                            Video (Arabic)
                                        </label>
                                        @if($resource->file_ar)
                                            <div class="mb-2">
                                                <small class="text-success"><i data-feather="check-circle" style="width: 12px; height: 12px;"></i> Current: {{ basename($resource->file_ar) }}</small>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="file_ar" accept="video/*,audio/*">
                                        <small class="text-muted">Upload Arabic version video/audio</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-light border">
                                        <label class="form-label small fw-bold text-success">
                                            <i data-feather="globe" style="width: 14px; height: 14px;"></i>
                                            Video (English)
                                        </label>
                                        @if($resource->file_en)
                                            <div class="mb-2">
                                                <small class="text-success"><i data-feather="check-circle" style="width: 12px; height: 12px;"></i> Current: {{ basename($resource->file_en) }}</small>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="file_en" accept="video/*,audio/*">
                                        <small class="text-muted">Upload English version video/audio</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <p class="text-muted small"><i data-feather="info" style="width: 14px; height: 14px;"></i> Add multiple videos below with Arabic and English versions:</p>

                            <!-- Affichage des vidéos multilingues existantes -->
                            @if($resource->video_files_multilingual && count($resource->video_files_multilingual) > 0)
                                <div class="mb-3">
                                    <h6 class="text-muted">Vidéos existantes (Multilingues) :</h6>
                                    @foreach($resource->video_files_multilingual as $index => $video)
                                        <div class="card mb-3 existing-multilingual-video-item" data-video-index="{{ $index }}">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                <span class="fw-bold small">Video #{{ $index + 1 }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-existing-multilingual-video">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i> Supprimer
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold text-primary">Titre (Arabe)</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="existing_ml_video_title_ar[{{ $index }}]"
                                                               value="{{ $video['title_ar'] ?? '' }}"
                                                               placeholder="عنوان الفيديو بالعربية">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold text-success">Titre (Anglais)</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="existing_ml_video_title_en[{{ $index }}]"
                                                               value="{{ $video['title_en'] ?? '' }}"
                                                               placeholder="Video title in English">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-primary">Fichier Vidéo (Arabe)</label>
                                                        @if(!empty($video['file_ar']))
                                                            <div class="mb-1">
                                                                <small class="text-success"><i data-feather="check-circle" style="width: 12px; height: 12px;"></i> {{ $video['file_ar'] }}</small>
                                                            </div>
                                                        @endif
                                                        <input type="file" class="form-control form-control-sm"
                                                               name="replace_ml_video_file_ar[{{ $index }}]"
                                                               accept="video/*,audio/*">
                                                        <input type="hidden" name="existing_ml_video_file_ar[{{ $index }}]" value="{{ $video['file_ar'] ?? '' }}">
                                                        <small class="text-muted">Remplacer (optionnel)</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-success">Fichier Vidéo (Anglais)</label>
                                                        @if(!empty($video['file_en']))
                                                            <div class="mb-1">
                                                                <small class="text-success"><i data-feather="check-circle" style="width: 12px; height: 12px;"></i> {{ $video['file_en'] }}</small>
                                                            </div>
                                                        @endif
                                                        <input type="file" class="form-control form-control-sm"
                                                               name="replace_ml_video_file_en[{{ $index }}]"
                                                               accept="video/*,audio/*">
                                                        <input type="hidden" name="existing_ml_video_file_en[{{ $index }}]" value="{{ $video['file_en'] ?? '' }}">
                                                        <small class="text-muted">Remplacer (optionnel)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" class="ml-video-delete-flag" name="delete_ml_video[{{ $index }}]" value="0">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Formulaire pour ajouter de nouvelles vidéos multilingues -->
                            <div id="multilingual-videos-container">
                                <div class="multilingual-video-input-group mb-3">
                                    <div class="card border-dashed">
                                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                            <span class="small fw-bold">Nouvelle Vidéo</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-multilingual-video" style="display: none;">
                                                <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-primary">Titre (Arabe)</label>
                                                    <input type="text" class="form-control form-control-sm" name="ml_video_title_ar[]"
                                                           placeholder="عنوان الفيديو بالعربية">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-success">Titre (Anglais)</label>
                                                    <input type="text" class="form-control form-control-sm" name="ml_video_title_en[]"
                                                           placeholder="Video title in English">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-primary">Fichier Vidéo (Arabe)</label>
                                                    <input type="file" class="form-control form-control-sm" name="ml_video_file_ar[]"
                                                           accept="video/*,audio/*">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small text-success">Fichier Vidéo (Anglais)</label>
                                                    <input type="file" class="form-control form-control-sm" name="ml_video_file_en[]"
                                                           accept="video/*,audio/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-multilingual-video">
                                <i data-feather="plus" class="me-1"></i>
                                Ajouter une vidéo multilingue
                            </button>
                            <div class="form-text">
                                Ajoutez des vidéos avec versions arabe et anglaise. Formats acceptés: MP4, AVI, MOV, MP3. Taille max: 10MB
                            </div>

                            {{-- Legacy video section - commented out
                            <hr class="my-3">
                            <p class="text-muted small"><i data-feather="alert-circle" style="width: 14px; height: 14px;"></i> Legacy videos (shown in all languages):</p>

                            <!-- Affichage des vidéos existantes (legacy) -->
                            @if($resource->video_files && count($resource->video_files) > 0)
                                <div class="mb-3">
                                    <h6 class="text-muted">Vidéos existantes (Legacy) :</h6>
                                    @foreach($resource->video_files as $index => $video)
                                        <div class="alert alert-info existing-video-item" data-video-index="{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <label class="form-label small mb-1">Titre</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="existing_video_titles[{{ $index }}]"
                                                               value="{{ $video['title'] }}"
                                                               placeholder="Titre de la vidéo">
                                                    </div>
                                                    <small class="text-muted">Fichier actuel: {{ $video['file'] }}</small>
                                                    <br>
                                                    <small class="text-muted">Remplacer le fichier (optionnel):</small>
                                                    <input type="file" class="form-control form-control-sm mt-1"
                                                           name="replace_video_files[{{ $index }}]"
                                                           accept="video/*,audio/*">
                                                    <input type="hidden" name="existing_video_files[{{ $index }}]" value="{{ $video['file'] }}">
                                                </div>
                                                <div class="d-flex gap-2 ms-3">
                                                    <a href="{{ route('resources.download-video', ['resource' => $resource, 'title' => $video['title']]) }}"
                                                       class="btn btn-sm btn-outline-success">
                                                        <i data-feather="download" class="me-1"></i>
                                                        Télécharger
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-existing-video">
                                                        <i data-feather="trash-2" class="me-1"></i>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" class="video-delete-flag" name="delete_video_files[{{ $index }}]" value="0">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Formulaire pour ajouter de nouvelles vidéos -->
                            <div id="videos-container">
                                <div class="video-input-group mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label small">Titre de la vidéo</label>
                                            <input type="text" class="form-control" name="video_titles[]"
                                                   placeholder="Ex: Introduction, Chapitre 1, etc.">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small">Fichier vidéo</label>
                                            <input type="file" class="form-control" name="video_files[]"
                                                   accept="video/*,audio/*">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label small">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-danger remove-video" style="display: none;">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-video">
                                <i data-feather="plus" class="me-1"></i>
                                Ajouter une vidéo
                            </button>
                            <div class="form-text">
                                Ajoutez de nouvelles vidéos avec titre et fichier. Formats acceptés: MP4, AVI, MOV, MP3. Taille max: 10MB
                            </div>
                            --}}
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i data-feather="save" class="me-2"></i>
                                Mettre à jour
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
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const videosSection = document.getElementById('videos-section');
    const videosContainer = document.getElementById('videos-container');
    const addVideoBtn = document.getElementById('add-video');

    // Fonction pour afficher/masquer la section vidéos et le champ fichier principal
    function toggleVideosSection() {
        const mainFileSection = document.getElementById('main-file-section');
        const mainFileArInput = document.getElementById('file_ar');
        const mainFileEnInput = document.getElementById('file_en');

        // Get video section inputs
        const videoSectionInputs = videosSection.querySelectorAll('input[type="file"]');

        if (typeSelect.value === 'video') {
            videosSection.style.display = 'block';
            mainFileSection.style.display = 'none';

            // Disable main file inputs so they don't get submitted
            if (mainFileArInput) mainFileArInput.disabled = true;
            if (mainFileEnInput) mainFileEnInput.disabled = true;

            // Enable video section inputs
            videoSectionInputs.forEach(input => input.disabled = false);
        } else {
            videosSection.style.display = 'none';
            mainFileSection.style.display = 'block';

            // Enable main file inputs
            if (mainFileArInput) mainFileArInput.disabled = false;
            if (mainFileEnInput) mainFileEnInput.disabled = false;

            // Disable video section inputs so they don't conflict
            videoSectionInputs.forEach(input => input.disabled = true);
        }
    }

    // Écouter les changements du type
    typeSelect.addEventListener('change', toggleVideosSection);

    // Initialiser l'état
    toggleVideosSection();

    /* Legacy video JavaScript - commented out
    // Fonction pour ajouter un champ vidéo
    function addVideoField() {
        const videoGroup = document.createElement('div');
        videoGroup.className = 'video-input-group mb-3';
        videoGroup.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label small">Titre de la vidéo</label>
                    <input type="text" class="form-control" name="video_titles[]"
                           placeholder="Ex: Introduction, Chapitre 1, etc.">
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Fichier vidéo</label>
                    <input type="file" class="form-control" name="video_files[]"
                           accept="video/*,audio/*">
                </div>
                <div class="col-md-1">
                    <label class="form-label small">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger remove-video">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
        `;

        // Ajouter l'événement de suppression
        const removeBtn = videoGroup.querySelector('.remove-video');
        removeBtn.addEventListener('click', function() {
            videoGroup.remove();
            updateRemoveButtons();
        });

        videosContainer.appendChild(videoGroup);
        updateRemoveButtons();

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Fonction pour mettre à jour l'affichage des boutons de suppression
    function updateRemoveButtons() {
        const videoGroups = videosContainer.querySelectorAll('.video-input-group');
        videoGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.remove-video');
            if (videoGroups.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }

    // Ajouter un champ vidéo au clic sur le bouton
    addVideoBtn.addEventListener('click', addVideoField);

    // Ajouter les événements de suppression aux boutons existants
    document.querySelectorAll('.remove-video').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.video-input-group').remove();
            updateRemoveButtons();
        });
    });

    // Initialiser l'état des boutons de suppression
    updateRemoveButtons();

    // Gérer la suppression des vidéos existantes
    document.querySelectorAll('.delete-existing-video').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoItem = this.closest('.existing-video-item');
            const deleteFlag = videoItem.querySelector('.video-delete-flag');

            if (confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
                deleteFlag.value = '1';
                videoItem.style.opacity = '0.5';
                videoItem.style.textDecoration = 'line-through';
                this.disabled = true;
                this.innerHTML = '<i data-feather="check"></i> Marqué pour suppression';

                // Réinitialiser Feather Icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    });
    */

    // Gérer la suppression des vidéos multilingues existantes
    document.querySelectorAll('.delete-existing-multilingual-video').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoItem = this.closest('.existing-multilingual-video-item');
            const deleteFlag = videoItem.querySelector('.ml-video-delete-flag');

            if (confirm('Êtes-vous sûr de vouloir supprimer cette vidéo multilingue ?')) {
                deleteFlag.value = '1';
                videoItem.style.opacity = '0.5';
                videoItem.querySelector('.card-body').style.textDecoration = 'line-through';
                this.disabled = true;
                this.innerHTML = '<i data-feather="check" style="width: 14px; height: 14px;"></i> Supprimé';

                // Réinitialiser Feather Icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    });

    // Gérer l'ajout de vidéos multilingues
    const multilingualVideosContainer = document.getElementById('multilingual-videos-container');
    const addMultilingualVideoBtn = document.getElementById('add-multilingual-video');

    function addMultilingualVideoField() {
        const videoGroup = document.createElement('div');
        videoGroup.className = 'multilingual-video-input-group mb-3';
        videoGroup.innerHTML = `
            <div class="card border-dashed">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold">Nouvelle Vidéo</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-multilingual-video">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Titre (Arabe)</label>
                            <input type="text" class="form-control form-control-sm" name="ml_video_title_ar[]"
                                   placeholder="عنوان الفيديو بالعربية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Titre (Anglais)</label>
                            <input type="text" class="form-control form-control-sm" name="ml_video_title_en[]"
                                   placeholder="Video title in English">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small text-primary">Fichier Vidéo (Arabe)</label>
                            <input type="file" class="form-control form-control-sm" name="ml_video_file_ar[]"
                                   accept="video/*,audio/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-success">Fichier Vidéo (Anglais)</label>
                            <input type="file" class="form-control form-control-sm" name="ml_video_file_en[]"
                                   accept="video/*,audio/*">
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Ajouter l'événement de suppression
        const removeBtn = videoGroup.querySelector('.remove-multilingual-video');
        removeBtn.addEventListener('click', function() {
            videoGroup.remove();
            updateMultilingualRemoveButtons();
        });

        multilingualVideosContainer.appendChild(videoGroup);
        updateMultilingualRemoveButtons();

        // Réinitialiser Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function updateMultilingualRemoveButtons() {
        const videoGroups = multilingualVideosContainer.querySelectorAll('.multilingual-video-input-group');
        videoGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.remove-multilingual-video');
            if (videoGroups.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }

    if (addMultilingualVideoBtn) {
        addMultilingualVideoBtn.addEventListener('click', addMultilingualVideoField);
    }

    // Ajouter les événements de suppression aux boutons multilingues existants
    document.querySelectorAll('.remove-multilingual-video').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.multilingual-video-input-group').remove();
            updateMultilingualRemoveButtons();
        });
    });

    // Initialiser l'état des boutons multilingues
    updateMultilingualRemoveButtons();

    // Gérer la suppression du fichier principal
    const deleteMainFileBtn = document.getElementById('delete-main-file');
    const deleteMainFileFlag = document.getElementById('delete-main-file-flag');
    const mainFileInput = document.getElementById('file');
    const mainFileSection = document.getElementById('main-file-section');
    const fileLabelText = document.getElementById('file-label-text');
    const fileHelpText = document.getElementById('file-help-text');

    if (deleteMainFileBtn) {
        deleteMainFileBtn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer le fichier actuel ? Vous devrez télécharger un nouveau fichier.')) {
                deleteMainFileFlag.value = '1';

                // Activer la validation HTML5 pour forcer l'upload
                mainFileInput.setAttribute('required', 'required');

                // Mettre en évidence le champ de téléchargement
                mainFileSection.style.display = 'block';
                mainFileSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Mettre à jour le label et le style
                fileLabelText.innerHTML = 'Nouveau fichier <span class="text-danger">*OBLIGATOIRE*</span>';
                mainFileInput.classList.add('border-danger', 'border-2');
                fileHelpText.classList.add('text-danger', 'fw-bold');

                // Mettre à jour visuellement l'alerte du fichier actuel
                const alertDiv = this.closest('.alert');
                alertDiv.classList.remove('alert-info');
                alertDiv.classList.add('alert-warning');
                alertDiv.innerHTML = '<div class="d-flex align-items-center"><i data-feather="alert-triangle" class="me-2"></i><strong>Fichier marqué pour suppression - Veuillez télécharger un nouveau fichier ci-dessus</strong></div>';

                // Désactiver le bouton
                this.disabled = true;

                // Réinitialiser Feather Icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    }
});
</script>
@endpush
