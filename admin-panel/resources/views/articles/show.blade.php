@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Article Details</h4>
                            <p class="text-white-50 mb-0">Complete information about the article "{{ $article->titre_en }}"</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="info" class="me-2"></i>
                                        General information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Article ID</label>
                                            <p class="mb-0">{{ $article->id }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Slug</label>
                                            <p class="mb-0">{{ $article->slug }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <span class="badge {{ $article->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $article->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Views</label>
                                            <p class="mb-0">{{ $article->views_count }} views</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Article image -->
                    @if($article->image)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="image" class="me-2"></i>
                                        Article image
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset('uploads/articles/' . $article->image) }}" alt="Article image" class="img-fluid rounded" style="max-height: 400px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Arabic content -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Arabic Content
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Arabic Title</label>
                                            <p class="mb-0">{{ $article->titre_ar }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Arabic Meta Title</label>
                                            <p class="mb-0">{{ $article->meta_title_ar }}</p>
                                        </div>
                                        @if($article->description_short_ar)
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Short Description (Arabic)</label>
                                            <p class="mb-0">{{ $article->description_short_ar }}</p>
                                        </div>
                                        @endif
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Arabic Description</label>
                                            <div style="direction: rtl; text-align: right;">
                                                {!! $article->description_ar !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- English content -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        English Content
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">English Title</label>
                                            <p class="mb-0">{{ $article->titre_en }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">English Meta Title</label>
                                            <p class="mb-0">{{ $article->meta_title_en }}</p>
                                        </div>
                                        @if($article->description_short_en)
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Short Description (English)</label>
                                            <p class="mb-0">{{ $article->description_short_en }}</p>
                                        </div>
                                        @endif
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">English Description</label>
                                            <div style="direction: ltr; text-align: left;">
                                                {!! $article->description_en !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur l'auteur -->
                    @if($article->author_ar || $article->author_en)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="user" class="me-2"></i>
                                        Informations sur l'auteur
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($article->author_ar)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Auteur (Arabe)</label>
                                            <p class="mb-0">{{ $article->author_ar }}</p>
                                        </div>
                                        @endif
                                        @if($article->author_en)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Auteur (Anglais)</label>
                                            <p class="mb-0">{{ $article->author_en }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Dates et métadonnées -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="calendar" class="me-2"></i>
                                        Dates et métadonnées
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Date de création</label>
                                            <p class="mb-0">{{ $article->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Dernière modification</label>
                                            <p class="mb-0">{{ $article->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        @if($article->published_date)
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Date de publication</label>
                                            <p class="mb-0">{{ $article->published_date->format('d/m/Y') }}</p>
                                        </div>
                                        @endif
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">URL de l'article</label>
                                            <p class="mb-0">
                                                <a href="{{ url('/articles/' . $article->slug) }}" target="_blank" class="text-decoration-none">
                                                    Voir l'article
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Back to list
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-warning">
                                        <i data-feather="edit" class="me-2"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this article?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i data-feather="trash-2" class="me-2"></i>
                                            Delete
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
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.fw-bold {
    font-weight: 600 !important;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-warning {
    background-color: #ff9f43;
    border-color: #ff9f43;
}

.btn-warning:hover {
    background-color: #f39c12;
    border-color: #f39c12;
}

.btn-danger {
    background-color: #ea5455;
    border-color: #ea5455;
}

.btn-danger:hover {
    background-color: #d73b3b;
    border-color: #d73b3b;
}
</style>
@endsection
