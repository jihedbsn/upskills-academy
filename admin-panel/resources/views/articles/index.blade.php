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
                                <h4 class="card-title mb-1">Article Management</h4>
                                <p class="text-white-50 mb-0">Manage your multilingual articles</p>
                            </div>
                        </div>
                        <a href="{{ route('articles.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            New Article
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

                    <!-- Filters and search -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('articles.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search in articles..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Articles table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title (AR)</th>
                                    <th>Title (EN)</th>
                                    <th>Author</th>
                                    <th>Published date</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr>
                                        <td>{{ $article->id }}</td>
                                        <td>
                                            @if($article->image)
                                                <img src="{{ asset('uploads/articles/' . $article->image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i data-feather="image" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($article->titre_ar, 30) }}</div>
                                            <small class="text-muted">{{ Str::limit($article->meta_title_ar, 25) }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($article->titre_en, 30) }}</div>
                                            <small class="text-muted">{{ Str::limit($article->meta_title_en, 25) }}</small>
                                        </td>
                                        <td>
                                            @if($article->author_ar || $article->author_en)
                                                <span class="badge bg-info">{{ $article->author_ar ?: $article->author_en }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($article->published_date)
                                                <span class="badge bg-success">{{ $article->published_date->format('Y-m-d') }}</span>
                                            @else
                                                <span class="text-muted">Unpublished</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $article->views_count }} views</span>
                                        </td>
                                        <td>
                                            @if($article->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('articles.show', $article) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('articles.edit', $article) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                                                <p>No articles found</p>
                                                <a href="{{ route('articles.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Create the first article
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $articles->appends(request()->query())->links() }}
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
