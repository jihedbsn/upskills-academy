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
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Gestion des Articles de Blog</h4>
                                <p class="text-white-50 mb-0">Gérez vos articles de blog multilingues</p>
                            </div>
                        </div>
                        <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            Nouvel Article
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres et recherche -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('blogs.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher dans les articles..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tableau des articles -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Titre (AR)</th>
                                    <th>Titre (EN)</th>
                                    <th>Auteur</th>
                                    <th>Date de publication</th>
                                    <th>Vues</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blogs as $blog)
                                    <tr>
                                        <td>{{ $blog->id }}</td>
                                        <td>
                                            @if($blog->image)
                                                <img src="{{ asset('uploads/blogs/' . $blog->image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i data-feather="image" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($blog->titre_ar, 30) }}</div>
                                            <small class="text-muted">{{ Str::limit($blog->meta_title_ar, 25) }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($blog->titre_en, 30) }}</div>
                                            <small class="text-muted">{{ Str::limit($blog->meta_title_en, 25) }}</small>
                                        </td>
                                        <td>
                                            @if($blog->author_ar || $blog->author_en)
                                                <span class="badge bg-info">{{ $blog->author_ar ?: $blog->author_en }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($blog->published_date)
                                                <span class="badge bg-success">{{ $blog->published_date->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-muted">Non publié</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $blog->views_count }} vues</span>
                                        </td>
                                        <td>
                                            @if($blog->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="file-text" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>Aucun article de blog trouvé</p>
                                                <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Créer le premier article
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($blogs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $blogs->appends(request()->query())->links() }}
                        </div>
                    @endif
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #6e6b7b;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
