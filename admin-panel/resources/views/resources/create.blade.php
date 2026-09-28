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
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">New Resource</h4>
                            <p class="text-white-50 mb-0">Add a new resource to the system</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Validation Errors</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('resources.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name_ar" class="form-label">
                                        <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Name (Arabic) *
                                    </label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                           id="name_ar" name="name_ar" value="{{ old('name_ar') }}"
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
                                        Name (English) *
                                    </label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                           id="name_en" name="name_en" value="{{ old('name_en') }}"
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
                                Resource Type
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select a type</option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>
                                    <i data-feather="video" class="me-2"></i>Video
                                </option>
                                <option value="book" {{ old('type') == 'book' ? 'selected' : '' }}>
                                    <i data-feather="book" class="me-2"></i>Book
                                </option>
                                <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>
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
                                        File (Arabic)
                                    </label>
                                    <input type="file" class="form-control @error('file_ar') is-invalid @enderror"
                                           id="file_ar" name="file_ar">
                                    @error('file_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="file_en" class="form-label">
                                        <i data-feather="upload" class="me-2" style="width: 16px; height: 16px;"></i>
                                        File (English)
                                    </label>
                                    <input type="file" class="form-control @error('file_en') is-invalid @enderror"
                                           id="file_en" name="file_en">
                                    @error('file_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                Upload files for each language. If only one is provided, it will be used for both languages.
                                <br>Accepted formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG, GIF, MP4, AVI, MOV. Max size: 10MB
                            </div>
                        </div>

                        <!-- Section pour les vidéos multiples (visible seulement si type = video) -->
                        <div class="mb-4" id="videos-section" style="display: none;">
                            <label class="form-label">
                                <i data-feather="video" class="me-2" style="width: 16px; height: 16px;"></i>
                                Video/Audio Files
                            </label>

                            <!-- Multilingual Video/Audio Files -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="alert alert-light border">
                                        <label class="form-label small fw-bold text-primary">
                                            <i data-feather="globe" style="width: 14px; height: 14px;"></i>
                                            Video/Audio (Arabic)
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="file_ar" accept="video/*,audio/*">
                                        <small class="text-muted">Upload Arabic version video/audio</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-light border">
                                        <label class="form-label small fw-bold text-success">
                                            <i data-feather="globe" style="width: 14px; height: 14px;"></i>
                                            Video/Audio (English)
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="file_en" accept="video/*,audio/*">
                                        <small class="text-muted">Upload English version video/audio</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <p class="text-muted small"><i data-feather="info" style="width: 14px; height: 14px;"></i> Add multiple videos below with Arabic and English versions:</p>

                            <!-- Formulaire pour ajouter de nouvelles vidéos multilingues -->
                            <div id="multilingual-videos-container">
                                <div class="multilingual-video-input-group mb-3">
                                    <div class="card border-dashed">
                                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                            <span class="small fw-bold">New Multilingual Video</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-multilingual-video" style="display: none;">
                                                <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-primary">Title (Arabic)</label>
                                                    <input type="text" class="form-control form-control-sm" name="ml_video_title_ar[]"
                                                           placeholder="عنوان الفيديو بالعربية">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-success">Title (English)</label>
                                                    <input type="text" class="form-control form-control-sm" name="ml_video_title_en[]"
                                                           placeholder="Video title in English">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-primary">Video File (Arabic)</label>
                                                    <input type="file" class="form-control form-control-sm" name="ml_video_file_ar[]"
                                                           accept="video/*,audio/*">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small text-success">Video File (English)</label>
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
                                Add multilingual video
                            </button>
                            <div class="form-text">
                                Add videos with Arabic and English versions. Accepted formats: MP4, AVI, MOV, MP3. Max size: 10MB
                            </div>

                            {{-- Legacy video section - commented out
                            <hr class="my-3">
                            <p class="text-muted small"><i data-feather="alert-circle" style="width: 14px; height: 14px;"></i> Or add legacy videos (shown in all languages):</p>

                            <div id="videos-container">
                                <div class="video-input-group mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label small">Video Title</label>
                                            <input type="text" class="form-control" name="video_titles[]"
                                                   placeholder="Ex: Introduction, Chapter 1, etc.">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small">Video File</label>
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
                                Add legacy video
                            </button>
                            <div class="form-text text-muted">
                                Legacy videos are shown to all users regardless of language.
                            </div>
                            --}}
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i data-feather="save" class="me-2"></i>
                                Create Resource
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
                    <label class="form-label small">Video Title</label>
                    <input type="text" class="form-control" name="video_titles[]"
                           placeholder="Ex: Introduction, Chapter 1, etc.">
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Video File</label>
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

    // Initialiser l'état des boutons de suppression
    updateRemoveButtons();
    */

    // ===== MULTILINGUAL VIDEOS FUNCTIONALITY =====
    const multilingualVideosContainer = document.getElementById('multilingual-videos-container');
    const addMultilingualVideoBtn = document.getElementById('add-multilingual-video');

    // Function to add a multilingual video field
    function addMultilingualVideoField() {
        const videoGroup = document.createElement('div');
        videoGroup.className = 'multilingual-video-input-group mb-3';
        videoGroup.innerHTML = `
            <div class="card border-dashed">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold">New Multilingual Video</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-multilingual-video">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Title (Arabic)</label>
                            <input type="text" class="form-control form-control-sm" name="ml_video_title_ar[]"
                                   placeholder="عنوان الفيديو بالعربية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Title (English)</label>
                            <input type="text" class="form-control form-control-sm" name="ml_video_title_en[]"
                                   placeholder="Video title in English">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small text-primary">Video File (Arabic)</label>
                            <input type="file" class="form-control form-control-sm" name="ml_video_file_ar[]"
                                   accept="video/*,audio/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-success">Video File (English)</label>
                            <input type="file" class="form-control form-control-sm" name="ml_video_file_en[]"
                                   accept="video/*,audio/*">
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add remove event
        const removeBtn = videoGroup.querySelector('.remove-multilingual-video');
        removeBtn.addEventListener('click', function() {
            videoGroup.remove();
            updateMultilingualRemoveButtons();
        });

        multilingualVideosContainer.appendChild(videoGroup);
        updateMultilingualRemoveButtons();

        // Reinitialize Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Function to update multilingual remove buttons visibility
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

    // Add multilingual video on button click
    addMultilingualVideoBtn.addEventListener('click', addMultilingualVideoField);

    // Initialize multilingual remove buttons state
    updateMultilingualRemoveButtons();
});
</script>
@endpush
